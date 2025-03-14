<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::redirect('/', '/games')->name('dashboard');



Route::middleware(['auth'])->group(function () {

    Route::get('/games/search', [GameController::class, 'search'])->name('games.search');
    Route::get('/games/year', [GameController::class, 'yearFilterGames'])->name('games.yearFilter');
    Route::get('/games/genre', [GameController::class, 'genreFilterGames'])->name('games.genreFilter');
    Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');
    Route::post('/games/like', [GameController::class, 'toggleLike'])->name('games.like.toggle');

    Route::resource('games', GameController::class);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
