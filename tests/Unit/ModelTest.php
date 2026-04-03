<?php

use App\Models\Terminumfrage;
use App\Models\Textoptionenumfrage;

test('terminumfrage generates code on creation', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);
    expect($umfrage->code)->not->toBeEmpty();
    expect(strlen($umfrage->code))->toBe(16);
});

test('textoptionenumfrage generates code on creation', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);
    expect($umfrage->code)->not->toBeEmpty();
    expect(strlen($umfrage->code))->toBe(16);
});

test('terminumfrage casts booleans correctly', function () {
    $umfrage = Terminumfrage::create(['titel' => 'Test']);
    $umfrage->refresh();
    expect($umfrage->ist_aktiv)->toBeTrue();
    expect($umfrage->ist_abgeschlossen)->toBeFalse();
});

test('textoptionenumfrage casts booleans correctly', function () {
    $umfrage = Textoptionenumfrage::create(['titel' => 'Test']);
    $umfrage->refresh();
    expect($umfrage->ist_aktiv)->toBeTrue();
    expect($umfrage->ist_abgeschlossen)->toBeFalse();
});
