<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TerminumfrageController;
use App\Http\Controllers\TextoptionumfrageController;
use Illuminate\Support\Facades\Route;

// Startseite → Login
Route::get('/', fn () => redirect()->route('login'));

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Terminumfrage - öffentlich (Abstimmung)
Route::get('/terminumfrage/{code}', [TerminumfrageController::class, 'show'])->name('terminumfrage.show');
Route::post('/terminumfrage/{code}', [TerminumfrageController::class, 'vote'])->name('terminumfrage.vote');

// Textoptionumfrage - öffentlich (Abstimmung)
Route::get('/textoptionumfrage/{code}', [TextoptionumfrageController::class, 'show'])->name('textoptionumfrage.show');
Route::post('/textoptionumfrage/{code}', [TextoptionumfrageController::class, 'vote'])->name('textoptionumfrage.vote');

// Admin-Bereich (geschützt)
Route::middleware('auth')->group(function () {
    Route::get('/terminumfrage-erstellen', [TerminumfrageController::class, 'create'])->name('terminumfrage.create');
    Route::post('/terminumfrage-erstellen', [TerminumfrageController::class, 'store'])->name('terminumfrage.store');

    Route::get('/textoptionumfrage-erstellen', [TextoptionumfrageController::class, 'create'])->name('textoptionumfrage.create');
    Route::post('/textoptionumfrage-erstellen', [TextoptionumfrageController::class, 'store'])->name('textoptionumfrage.store');
});
