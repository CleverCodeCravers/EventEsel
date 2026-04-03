<?php

use App\Models\Admin;
use App\Models\Konsensumfrage;
use App\Models\Konsensteilnehmer;

beforeEach(function () {
    $this->admin = Admin::create(['username' => 'admin', 'password' => 'secret']);
});

// Erstellen
test('admin can view create form', function () {
    $this->actingAs($this->admin)
        ->get(route('konsensumfrage.create'))
        ->assertStatus(200)
        ->assertSee('Neue Konsensumfrage erstellen');
});

test('admin can create konsensumfrage with optionen and teilnehmer', function () {
    $this->actingAs($this->admin)
        ->post(route('konsensumfrage.store'), [
            'titel' => 'Teamessen',
            'beschreibung' => 'Wo gehen wir hin?',
            'optionen' => ['Pizza', 'Sushi', 'Burger'],
            'teilnehmer' => ['Alice', 'Bob', 'Charlie'],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('konsensumfragen', ['titel' => 'Teamessen']);
    $this->assertDatabaseCount('konsensoptionen', 3);
    $this->assertDatabaseCount('konsensteilnehmer', 3);
});

test('creating requires at least 2 optionen', function () {
    $this->actingAs($this->admin)
        ->post(route('konsensumfrage.store'), [
            'titel' => 'Test',
            'optionen' => ['Nur eine'],
            'teilnehmer' => ['Alice', 'Bob'],
        ])
        ->assertSessionHasErrors('optionen');
});

test('creating requires at least 2 teilnehmer', function () {
    $this->actingAs($this->admin)
        ->post(route('konsensumfrage.store'), [
            'titel' => 'Test',
            'optionen' => ['A', 'B'],
            'teilnehmer' => ['Nur einer'],
        ])
        ->assertSessionHasErrors('teilnehmer');
});

test('teilnehmer names must be unique', function () {
    $this->actingAs($this->admin)
        ->post(route('konsensumfrage.store'), [
            'titel' => 'Test',
            'optionen' => ['A', 'B'],
            'teilnehmer' => ['Alice', 'Alice'],
        ])
        ->assertSessionHasErrors('teilnehmer');
});

test('each teilnehmer gets a unique code', function () {
    $this->actingAs($this->admin)
        ->post(route('konsensumfrage.store'), [
            'titel' => 'Test',
            'optionen' => ['A', 'B'],
            'teilnehmer' => ['Alice', 'Bob'],
        ]);

    $codes = Konsensteilnehmer::pluck('code');
    expect($codes->unique()->count())->toBe(2);
});

// Links-Seite
test('admin sees links page after creation', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test']);
    $umfrage->optionen()->create(['text' => 'A']);
    $t = $umfrage->teilnehmer()->create(['name' => 'Alice']);

    $this->actingAs($this->admin)
        ->get(route('konsensumfrage.links', $umfrage))
        ->assertStatus(200)
        ->assertSee('Alice')
        ->assertSee($t->code);
});

// Abstimmen
test('teilnehmer can view voting page via personal link', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Teamessen']);
    $umfrage->optionen()->create(['text' => 'Pizza']);
    $t = $umfrage->teilnehmer()->create(['name' => 'Alice']);

    $this->get(route('konsensumfrage.show', [$umfrage->code, $t->code]))
        ->assertStatus(200)
        ->assertSee('Teamessen')
        ->assertSee('Alice')
        ->assertSee('Pizza');
});

test('teilnehmer can vote for exactly one option', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test']);
    $option = $umfrage->optionen()->create(['text' => 'Pizza']);
    $t = $umfrage->teilnehmer()->create(['name' => 'Alice']);

    $this->post(route('konsensumfrage.vote', [$umfrage->code, $t->code]), [
        'option' => $option->id,
    ])->assertRedirect()->assertSessionHas('success');

    expect($t->fresh()->hat_abgestimmt)->toBeTrue();
    expect($t->fresh()->konsensoption_id)->toBe($option->id);
});

test('teilnehmer cannot vote twice', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test']);
    $option1 = $umfrage->optionen()->create(['text' => 'A']);
    $option2 = $umfrage->optionen()->create(['text' => 'B']);
    $t = $umfrage->teilnehmer()->create(['name' => 'Alice', 'hat_abgestimmt' => true, 'konsensoption_id' => $option1->id]);

    $this->post(route('konsensumfrage.vote', [$umfrage->code, $t->code]), [
        'option' => $option2->id,
    ])->assertRedirect()->assertSessionHasErrors('umfrage');

    expect($t->fresh()->konsensoption_id)->toBe($option1->id);
});

test('voting on closed konsensumfrage is rejected', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test', 'ist_abgeschlossen' => true]);
    $option = $umfrage->optionen()->create(['text' => 'A']);
    $t = $umfrage->teilnehmer()->create(['name' => 'Alice']);

    $this->post(route('konsensumfrage.vote', [$umfrage->code, $t->code]), [
        'option' => $option->id,
    ])->assertRedirect()->assertSessionHasErrors('umfrage');

    expect($t->fresh()->hat_abgestimmt)->toBeFalse();
});

test('invalid teilnehmer code returns 404', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test']);

    $this->get(route('konsensumfrage.show', [$umfrage->code, 'invalid']))
        ->assertStatus(404);
});

// Ergebnis-Anzeige
test('results show vote counts and percentages', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test']);
    $option1 = $umfrage->optionen()->create(['text' => 'Pizza']);
    $option2 = $umfrage->optionen()->create(['text' => 'Sushi']);
    $t1 = $umfrage->teilnehmer()->create(['name' => 'Alice', 'hat_abgestimmt' => true, 'konsensoption_id' => $option1->id]);
    $t2 = $umfrage->teilnehmer()->create(['name' => 'Bob', 'hat_abgestimmt' => true, 'konsensoption_id' => $option1->id]);
    $t3 = $umfrage->teilnehmer()->create(['name' => 'Charlie']);

    $this->get(route('konsensumfrage.show', [$umfrage->code, $t3->code]))
        ->assertSee('2 von 3 haben abgestimmt')
        ->assertSee('2 Stimmen')
        ->assertSee('67%');
});

test('results do not reveal who voted what (secret vote)', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test']);
    $option = $umfrage->optionen()->create(['text' => 'Pizza']);
    $t1 = $umfrage->teilnehmer()->create(['name' => 'Alice', 'hat_abgestimmt' => true, 'konsensoption_id' => $option->id]);
    $t2 = $umfrage->teilnehmer()->create(['name' => 'Bob']);

    $response = $this->get(route('konsensumfrage.show', [$umfrage->code, $t2->code]));
    // The page should NOT show "Alice" next to "Pizza"
    $response->assertDontSee('Alice</td>');
});

// Dashboard-Integration
test('konsensumfrage appears in dashboard', function () {
    Konsensumfrage::create(['titel' => 'Teamessen']);

    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertSee('Teamessen')
        ->assertSee('Konsensumfrage');
});

// Admin close/delete
test('admin can close konsensumfrage', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test']);

    $this->actingAs($this->admin)
        ->patch(route('konsensumfrage.close', $umfrage))
        ->assertRedirect(route('dashboard'));

    expect($umfrage->fresh()->ist_abgeschlossen)->toBeTrue();
});

test('admin can delete konsensumfrage', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test']);
    $umfrage->optionen()->create(['text' => 'A']);
    $umfrage->teilnehmer()->create(['name' => 'Alice']);

    $this->actingAs($this->admin)
        ->delete(route('konsensumfrage.destroy', $umfrage))
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseCount('konsensumfragen', 0);
    $this->assertDatabaseCount('konsensoptionen', 0);
    $this->assertDatabaseCount('konsensteilnehmer', 0);
});

// Edit
test('admin can edit konsensumfrage', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Alt']);
    $option = $umfrage->optionen()->create(['text' => 'A']);
    $t = $umfrage->teilnehmer()->create(['name' => 'Alice']);

    $this->actingAs($this->admin)
        ->get(route('konsensumfrage.edit', $umfrage))
        ->assertStatus(200)
        ->assertSee('Alt')
        ->assertSee('Alice');
});

test('admin can update konsensumfrage and add teilnehmer', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Alt']);
    $option = $umfrage->optionen()->create(['text' => 'A']);
    $t = $umfrage->teilnehmer()->create(['name' => 'Alice']);

    $this->actingAs($this->admin)
        ->put(route('konsensumfrage.update', $umfrage), [
            'titel' => 'Neu',
            'bestehende_optionen' => [$option->id],
            'bestehende_teilnehmer' => [$t->id],
            'neue_teilnehmer' => ['Bob'],
        ])
        ->assertRedirect(route('dashboard'));

    expect($umfrage->fresh()->titel)->toBe('Neu');
    expect($umfrage->teilnehmer()->count())->toBe(2);
});

test('cannot remove teilnehmer who already voted', function () {
    $umfrage = Konsensumfrage::create(['titel' => 'Test']);
    $option = $umfrage->optionen()->create(['text' => 'A']);
    $t = $umfrage->teilnehmer()->create(['name' => 'Alice', 'hat_abgestimmt' => true, 'konsensoption_id' => $option->id]);

    $this->actingAs($this->admin)
        ->put(route('konsensumfrage.update', $umfrage), [
            'titel' => 'Test',
            'bestehende_optionen' => [$option->id],
            'bestehende_teilnehmer' => [], // try to remove Alice
        ]);

    // Alice should still exist because she already voted
    $this->assertDatabaseHas('konsensteilnehmer', ['id' => $t->id]);
});
