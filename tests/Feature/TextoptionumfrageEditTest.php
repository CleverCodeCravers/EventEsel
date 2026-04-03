<?php

use App\Models\Admin;
use App\Models\TextoptionAntwort;
use App\Models\Textoptionenumfrage;

beforeEach(function () {
    $this->admin = Admin::create(['username' => 'admin', 'password' => 'secret']);
});

test('edit page shows prefilled data', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Filmwahl', 'beschreibung' => 'Welcher Film?']);
    $umfrage->textoptionen()->create(['text' => 'Matrix']);

    $this->actingAs($this->admin)
        ->get(route('textoptionumfrage.edit', $umfrage))
        ->assertStatus(200)
        ->assertSee('Filmwahl')
        ->assertSee('Welcher Film?')
        ->assertSee('Matrix');
});

test('edit requires authentication', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);

    $this->get(route('textoptionumfrage.edit', $umfrage))
        ->assertRedirect(route('login'));
});

test('admin can update titel and beschreibung', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Alt']);
    $option = $umfrage->textoptionen()->create(['text' => 'Option']);

    $this->actingAs($this->admin)
        ->put(route('textoptionumfrage.update', $umfrage), [
            'titel' => 'Neu',
            'beschreibung' => 'Neue Beschreibung',
            'bestehende_optionen' => [$option->id],
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');

    expect($umfrage->fresh()->titel)->toBe('Neu');
});

test('admin can add new optionen', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);
    $option = $umfrage->textoptionen()->create(['text' => 'Alt']);

    $this->actingAs($this->admin)
        ->put(route('textoptionumfrage.update', $umfrage), [
            'titel' => 'Test',
            'bestehende_optionen' => [$option->id],
            'neue_optionen' => ['Neu 1', 'Neu 2'],
        ]);

    expect($umfrage->textoptionen()->count())->toBe(3);
});

test('admin can remove optionen', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);
    $option1 = $umfrage->textoptionen()->create(['text' => 'Behalten']);
    $option2 = $umfrage->textoptionen()->create(['text' => 'Entfernen']);

    $this->actingAs($this->admin)
        ->put(route('textoptionumfrage.update', $umfrage), [
            'titel' => 'Test',
            'bestehende_optionen' => [$option1->id],
        ]);

    expect($umfrage->textoptionen()->count())->toBe(1);
    $this->assertDatabaseMissing('textoptionen', ['id' => $option2->id]);
});

test('removing option cascades to antworten', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);
    $option = $umfrage->textoptionen()->create(['text' => 'Option']);
    TextoptionAntwort::create(['textoption_id' => $option->id, 'teilnehmer' => 'Max']);

    $this->actingAs($this->admin)
        ->put(route('textoptionumfrage.update', $umfrage), [
            'titel' => 'Test',
            'bestehende_optionen' => [],
            'neue_optionen' => ['Neue Option'],
        ]);

    $this->assertDatabaseMissing('textoptionen', ['id' => $option->id]);
    $this->assertDatabaseMissing('textoption_antworten', ['textoption_id' => $option->id]);
});

test('antworten for kept optionen are preserved', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);
    $option = $umfrage->textoptionen()->create(['text' => 'Option']);
    TextoptionAntwort::create(['textoption_id' => $option->id, 'teilnehmer' => 'Max']);

    $this->actingAs($this->admin)
        ->put(route('textoptionumfrage.update', $umfrage), [
            'titel' => 'Test',
            'bestehende_optionen' => [$option->id],
        ]);

    $this->assertDatabaseHas('textoption_antworten', ['textoption_id' => $option->id, 'teilnehmer' => 'Max']);
});

test('code does not change on update', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);
    $option = $umfrage->textoptionen()->create(['text' => 'Option']);
    $originalCode = $umfrage->code;

    $this->actingAs($this->admin)
        ->put(route('textoptionumfrage.update', $umfrage), [
            'titel' => 'Neuer Titel',
            'bestehende_optionen' => [$option->id],
        ]);

    expect($umfrage->fresh()->code)->toBe($originalCode);
});

test('update validates titel required', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);

    $this->actingAs($this->admin)
        ->put(route('textoptionumfrage.update', $umfrage), [
            'titel' => '',
        ])
        ->assertSessionHasErrors('titel');
});

test('update validates beschreibung max length', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);

    $this->actingAs($this->admin)
        ->put(route('textoptionumfrage.update', $umfrage), [
            'titel' => 'Test',
            'beschreibung' => str_repeat('a', 1001),
        ])
        ->assertSessionHasErrors('beschreibung');
});
