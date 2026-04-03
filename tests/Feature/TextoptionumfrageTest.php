<?php

use App\Models\Admin;
use App\Models\TextoptionAntwort;
use App\Models\Textoptionenumfrage;

beforeEach(function () {
    $this->admin = Admin::create(['username' => 'admin', 'password' => 'secret']);
});

// R00004: Textoptionumfrage erstellen
test('admin can view create form', function () {
    $this->actingAs($this->admin)
        ->get(route('textoptionumfrage.create'))
        ->assertStatus(200)
        ->assertSee('Neue Textoptionumfrage erstellen');
});

test('admin can create a textoptionumfrage', function () {
    $this->actingAs($this->admin)
        ->post(route('textoptionumfrage.store'), [
            'titel' => 'Filmwahl',
            'beschreibung' => 'Welcher Film?',
            'optionen' => ['Matrix', 'Inception'],
        ])
        ->assertRedirect(route('textoptionumfrage.create'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('textoptionenumfragen', ['titel' => 'Filmwahl']);
    $this->assertDatabaseCount('textoptionen', 2);
});

test('creating textoptionumfrage requires titel', function () {
    $this->actingAs($this->admin)
        ->post(route('textoptionumfrage.store'), [
            'titel' => '',
            'optionen' => ['Option 1'],
        ])
        ->assertSessionHasErrors('titel');
});

test('creating textoptionumfrage requires at least one option', function () {
    $this->actingAs($this->admin)
        ->post(route('textoptionumfrage.store'), [
            'titel' => 'Test',
            'optionen' => [],
        ])
        ->assertSessionHasErrors('optionen');
});

test('titel must not exceed 200 characters', function () {
    $this->actingAs($this->admin)
        ->post(route('textoptionumfrage.store'), [
            'titel' => str_repeat('a', 201),
            'optionen' => ['Option'],
        ])
        ->assertSessionHasErrors('titel');
});

test('beschreibung must not exceed 1000 characters', function () {
    $this->actingAs($this->admin)
        ->post(route('textoptionumfrage.store'), [
            'titel' => 'Test',
            'beschreibung' => str_repeat('a', 1001),
            'optionen' => ['Option'],
        ])
        ->assertSessionHasErrors('beschreibung');
});

// R00005: Textoptionumfrage abstimmen
test('public user can view textoptionumfrage', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Filmwahl']);
    $umfrage->textoptionen()->create(['text' => 'Matrix']);

    $this->get(route('textoptionumfrage.show', $umfrage->code))
        ->assertStatus(200)
        ->assertSee('Filmwahl')
        ->assertSee('Matrix');
});

test('public user can vote on textoptionumfrage', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Filmwahl']);
    $option = $umfrage->textoptionen()->create(['text' => 'Matrix']);

    $this->post(route('textoptionumfrage.vote', $umfrage->code), [
        'teilnehmer' => 'Max',
        'optionen' => [$option->id],
    ])->assertRedirect(route('textoptionumfrage.show', $umfrage->code))
      ->assertSessionHas('success');

    $this->assertDatabaseHas('textoption_antworten', [
        'teilnehmer' => 'Max',
        'textoption_id' => $option->id,
    ]);
});

test('voting requires teilnehmer name', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);
    $option = $umfrage->textoptionen()->create(['text' => 'Option']);

    $this->post(route('textoptionumfrage.vote', $umfrage->code), [
        'teilnehmer' => '',
        'optionen' => [$option->id],
    ])->assertSessionHasErrors('teilnehmer');
});

test('invalid code returns 404', function () {
    $this->get(route('textoptionumfrage.show', 'nonexistent'))
        ->assertStatus(404);
});

// R00007: Ergebnis-Anzeige
test('results show voter names and counts', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Filmwahl']);
    $option = $umfrage->textoptionen()->create(['text' => 'Matrix']);

    TextoptionAntwort::create([
        'textoption_id' => $option->id,
        'teilnehmer' => 'Max',
    ]);

    $this->get(route('textoptionumfrage.show', $umfrage->code))
        ->assertSee('Max')
        ->assertSee('1');
});
