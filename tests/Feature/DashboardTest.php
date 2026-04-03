<?php

use App\Models\Admin;
use App\Models\TerminAntwort;
use App\Models\Terminumfrage;
use App\Models\Textoptionenumfrage;

beforeEach(function () {
    $this->admin = Admin::create(['username' => 'admin', 'password' => 'secret']);
});

test('dashboard requires authentication', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('dashboard shows all surveys', function () {
    Terminumfrage::create(['titel' => 'Filmabend']);
    Textoptionenumfrage::create(['titel' => 'Filmwahl']);

    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee('Filmabend')
        ->assertSee('Filmwahl')
        ->assertSee('Terminumfrage')
        ->assertSee('Textoptionumfrage');
});

test('dashboard shows empty state', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee('Noch keine Umfragen vorhanden');
});

test('login redirects to dashboard', function () {
    $this->post('/login', ['username' => 'admin', 'password' => 'secret'])
        ->assertRedirect(route('dashboard'));
});

// Abschließen
test('admin can close a terminumfrage', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);

    $this->actingAs($this->admin)
        ->patch(route('terminumfrage.close', $umfrage->id))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');

    expect($umfrage->fresh()->ist_abgeschlossen)->toBeTrue();
});

test('admin can close a textoptionumfrage', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);

    $this->actingAs($this->admin)
        ->patch(route('textoptionumfrage.close', $umfrage->id))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');

    expect($umfrage->fresh()->ist_abgeschlossen)->toBeTrue();
});

test('closed terminumfrage shows message on public page', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test', 'ist_abgeschlossen' => true]);
    $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);

    $this->get(route('terminumfrage.show', $umfrage->code))
        ->assertSee('Diese Umfrage ist abgeschlossen')
        ->assertDontSee('Abstimmen');
});

test('closed textoptionumfrage shows message on public page', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test', 'ist_abgeschlossen' => true]);
    $umfrage->textoptionen()->create(['text' => 'Option 1']);

    $this->get(route('textoptionumfrage.show', $umfrage->code))
        ->assertSee('Diese Umfrage ist abgeschlossen')
        ->assertDontSee('Abstimmen');
});

test('voting on closed terminumfrage is rejected', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test', 'ist_abgeschlossen' => true]);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);

    $this->post(route('terminumfrage.vote', $umfrage->code), [
        'teilnehmer' => 'Max',
        'termine' => [$termin->id],
    ])->assertRedirect()
      ->assertSessionHasErrors('umfrage');

    $this->assertDatabaseCount('termin_antworten', 0);
});

test('voting on closed textoptionumfrage is rejected', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test', 'ist_abgeschlossen' => true]);
    $option = $umfrage->textoptionen()->create(['text' => 'Option']);

    $this->post(route('textoptionumfrage.vote', $umfrage->code), [
        'teilnehmer' => 'Max',
        'optionen' => [$option->id],
    ])->assertRedirect()
      ->assertSessionHasErrors('umfrage');

    $this->assertDatabaseCount('textoption_antworten', 0);
});

// Löschen
test('admin can delete a terminumfrage', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);
    $antwort = TerminAntwort::create(['teilnehmer' => 'Max']);
    $antwort->moeglicheTermine()->attach($termin->id);

    $this->actingAs($this->admin)
        ->delete(route('terminumfrage.destroy', $umfrage->id))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');

    $this->assertDatabaseCount('terminumfragen', 0);
    $this->assertDatabaseCount('moegliche_termine', 0);
});

test('admin can delete a textoptionumfrage', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);
    $option = $umfrage->textoptionen()->create(['text' => 'Option']);

    $this->actingAs($this->admin)
        ->delete(route('textoptionumfrage.destroy', $umfrage->id))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');

    $this->assertDatabaseCount('textoptionenumfragen', 0);
    $this->assertDatabaseCount('textoptionen', 0);
});

test('dashboard shows closed surveys as visually distinct', function () {
    Terminumfrage::create(['titel' => 'Abgeschlossen', 'ist_abgeschlossen' => true]);
    Terminumfrage::create(['titel' => 'Aktiv']);

    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertSee('Abgeschlossen')
        ->assertSee('Aktiv');
});
