<?php

use App\Models\Admin;
use App\Models\TerminAntwort;
use App\Models\Terminumfrage;

beforeEach(function () {
    $this->admin = Admin::create(['username' => 'admin', 'password' => 'secret']);
});

test('edit page shows prefilled data', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Filmabend', 'beschreibung' => 'Wann?']);
    $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);

    $this->actingAs($this->admin)
        ->get(route('terminumfrage.edit', $umfrage))
        ->assertStatus(200)
        ->assertSee('Filmabend')
        ->assertSee('Wann?')
        ->assertSee('10.04.2026');
});

test('edit requires authentication', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);

    $this->get(route('terminumfrage.edit', $umfrage))
        ->assertRedirect(route('login'));
});

test('admin can update titel and beschreibung', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Alt', 'beschreibung' => 'Alt']);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);

    $this->actingAs($this->admin)
        ->put(route('terminumfrage.update', $umfrage), [
            'titel' => 'Neu',
            'beschreibung' => 'Neue Beschreibung',
            'bestehende_termine' => [$termin->id],
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');

    expect($umfrage->fresh()->titel)->toBe('Neu');
    expect($umfrage->fresh()->beschreibung)->toBe('Neue Beschreibung');
});

test('admin can add new termine', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);

    $this->actingAs($this->admin)
        ->put(route('terminumfrage.update', $umfrage), [
            'titel' => 'Test',
            'bestehende_termine' => [$termin->id],
            'neue_termine' => ['2026-04-15', '2026-04-20'],
        ]);

    expect($umfrage->moeglicheTermine()->count())->toBe(3);
});

test('admin can remove termine', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);
    $termin1 = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);
    $termin2 = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-12']);

    $this->actingAs($this->admin)
        ->put(route('terminumfrage.update', $umfrage), [
            'titel' => 'Test',
            'bestehende_termine' => [$termin1->id],
        ]);

    expect($umfrage->moeglicheTermine()->count())->toBe(1);
    $this->assertDatabaseMissing('moegliche_termine', ['id' => $termin2->id]);
});

test('removing termin cascades to antworten', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);
    $antwort = TerminAntwort::create(['teilnehmer' => 'Max']);
    $antwort->moeglicheTermine()->attach($termin->id);

    $this->actingAs($this->admin)
        ->put(route('terminumfrage.update', $umfrage), [
            'titel' => 'Test',
            'bestehende_termine' => [],
            'neue_termine' => ['2026-05-01'],
        ]);

    $this->assertDatabaseMissing('moegliche_termine', ['id' => $termin->id]);
    $this->assertDatabaseMissing('termin_antwort_moeglicher_termin', ['moeglicher_termin_id' => $termin->id]);
});

test('antworten for kept termine are preserved', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);
    $antwort = TerminAntwort::create(['teilnehmer' => 'Max']);
    $antwort->moeglicheTermine()->attach($termin->id);

    $this->actingAs($this->admin)
        ->put(route('terminumfrage.update', $umfrage), [
            'titel' => 'Test',
            'bestehende_termine' => [$termin->id],
        ]);

    expect($antwort->fresh()->moeglicheTermine)->toHaveCount(1);
});

test('code does not change on update', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);
    $termin = $umfrage->moeglicheTermine()->create(['datum' => '2026-04-10']);
    $originalCode = $umfrage->code;

    $this->actingAs($this->admin)
        ->put(route('terminumfrage.update', $umfrage), [
            'titel' => 'Neuer Titel',
            'bestehende_termine' => [$termin->id],
        ]);

    expect($umfrage->fresh()->code)->toBe($originalCode);
});

test('update validates titel required', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);

    $this->actingAs($this->admin)
        ->put(route('terminumfrage.update', $umfrage), [
            'titel' => '',
        ])
        ->assertSessionHasErrors('titel');
});
