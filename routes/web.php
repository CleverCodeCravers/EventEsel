<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/terminumfrage-erstellen', [TerminumfrageController::class, 'create'])->name('terminumfrage.create');
    Route::post('/terminumfrage-erstellen', [TerminumfrageController::class, 'store'])->name('terminumfrage.store');
    Route::get('/terminumfrage/{terminumfrage}/edit', [TerminumfrageController::class, 'edit'])->name('terminumfrage.edit');
    Route::put('/terminumfrage/{terminumfrage}', [TerminumfrageController::class, 'update'])->name('terminumfrage.update');
    Route::patch('/terminumfrage/{terminumfrage}/close', [DashboardController::class, 'closeTerminumfrage'])->name('terminumfrage.close');
    Route::delete('/terminumfrage/{terminumfrage}/delete', [DashboardController::class, 'destroyTerminumfrage'])->name('terminumfrage.destroy');

    Route::get('/textoptionumfrage-erstellen', [TextoptionumfrageController::class, 'create'])->name('textoptionumfrage.create');
    Route::post('/textoptionumfrage-erstellen', [TextoptionumfrageController::class, 'store'])->name('textoptionumfrage.store');
    Route::get('/textoptionumfrage/{textoptionenumfrage}/edit', [TextoptionumfrageController::class, 'edit'])->name('textoptionumfrage.edit');
    Route::put('/textoptionumfrage/{textoptionenumfrage}', [TextoptionumfrageController::class, 'update'])->name('textoptionumfrage.update');
    Route::patch('/textoptionumfrage/{textoptionenumfrage}/close', [DashboardController::class, 'closeTextoptionumfrage'])->name('textoptionumfrage.close');
    Route::delete('/textoptionumfrage/{textoptionenumfrage}/delete', [DashboardController::class, 'destroyTextoptionumfrage'])->name('textoptionumfrage.destroy');
});
