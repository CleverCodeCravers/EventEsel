<?php

use App\Models\Admin;
use App\Models\MoeglicherTermin;
use App\Models\TerminAntwort;
use App\Models\Terminumfrage;

beforeEach(function () {
    $this->admin = Admin::create(['username' => 'admin', 'password' => 'secret']);
});

// R00002: Terminumfrage erstellen
test('admin can view create form', function () {
    $this->actingAs($this->admin)
        ->get(route('terminumfrage.create'))
        ->assertStatus(200)
        ->assertSee('Neue Terminumfrage erstellen');
});

test('admin can create a terminumfrage', function () {
    $this->actingAs($this->admin)
        ->post(route('terminumfrage.store'), [
            'titel' => 'Filmabend',
            'beschreibung' => 'Wann passt es euch?',
            'termine' => ['2026-04-10', '2026-04-12'],
        ])
        ->assertRedirect(route('terminumfrage.create'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('terminumfragen', ['titel' => 'Filmabend']);
    $this->assertDatabaseCount('moegliche_termine', 2);
});

test('creating terminumfrage requires titel', function () {
    $this->actingAs($this->admin)
        ->post(route('terminumfrage.store'), [
            'titel' => '',
            'termine' => ['2026-04-10'],
        ])
        ->assertSessionHasErrors('titel');
});

test('creating terminumfrage requires at least one termin', function () {
    $this->actingAs($this->admin)
        ->post(route('terminumfrage.store'), [
            'titel' => 'Test',
            'termine' => [],
        ])
        ->assertSessionHasErrors('termine');
});

test('creating terminumfrage generates unique code', function () {
    $this->actingAs($this->admin)
        ->post(route('terminumfrage.store'), [
            'titel' => 'Test 1',
            'termine' => ['2026-04-10'],
        ]);

    $this->actingAs($this->admin)
        ->post(route('terminumfrage.store'), [
            'titel' => 'Test 2',
            'termine' => ['2026-04-11'],
        ]);

    $codes = Terminumfrage::pluck('code');
    expect($codes->unique()->count())->toBe(2);
});

// R00003: Terminumfrage abstimmen
test('public user can view terminumfrage', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Filmabend']);
    $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);

    $this->get(route('terminumfrage.show', $umfrage->code))
        ->assertStatus(200)
        ->assertSee('Filmabend')
        ->assertSee('10.04.2026');
});

test('public user can vote on terminumfrage', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Filmabend']);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);

    $this->post(route('terminumfrage.vote', $umfrage->code), [
        'teilnehmer' => 'Max',
        'termine' => [$termin->id],
    ])->assertRedirect(route('terminumfrage.show', $umfrage->code))
      ->assertSessionHas('success');

    $this->assertDatabaseHas('termin_antworten', ['teilnehmer' => 'Max']);
    expect(TerminAntwort::first()->moeglicheTermine)->toHaveCount(1);
});

test('voting requires teilnehmer name', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);

    $this->post(route('terminumfrage.vote', $umfrage->code), [
        'teilnehmer' => '',
        'termine' => [$termin->id],
    ])->assertSessionHasErrors('teilnehmer');
});

test('invalid code returns 404', function () {
    $this->get(route('terminumfrage.show', 'nonexistent'))
        ->assertStatus(404);
});

// R00007: Ergebnis-Anzeige
test('results show voter names and counts', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Filmabend']);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);

    $antwort = TerminAntwort::create(['teilnehmer' => 'Max']);
    $antwort->moeglicheTermine()->attach($termin->id);

    $this->get(route('terminumfrage.show', $umfrage->code))
        ->assertSee('Max')
        ->assertSee('1');
});
