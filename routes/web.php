<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

Route::get('/', \App\Livewire\Web\Home::class)->name('home');
Route::get('/cadastrar', \App\Livewire\Web\CreateFilm::class)->name('create');
Route::get('/ver', \App\Livewire\Web\ViewFilm::class)->name('see');
Route::get('/', [MovieController::class, 'index'])->name('movies.index');
Route::get('/search', [MovieController::class, 'search'])->name('movies.search');
Route::get('/movie/{imdbId}', [MovieController::class, 'show'])->name('movies.show');
Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');
Route::get('/registrados', [MovieController::class, 'registered'])->name('movies.registered');
Route::delete('/movie/{imdbId}', [MovieController::class, 'destroy'])->name('movies.destroy');
