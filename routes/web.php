<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LogLikesController;
use App\Http\Controllers\PlayedGameController;
use App\Http\Controllers\PlayLaterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::redirect('/', '/games')->name('dashboard');



Route::middleware(['auth'])->group(function () {

    Route::get('/games/search', [GameController::class, 'search'])->name('games.search');
    Route::get('/games/year', [GameController::class, 'yearFilterGames'])->name('games.yearFilter');
    Route::get('/games/genre', [GameController::class, 'genreFilterGames'])->name('games.genreFilter');
    Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');
    Route::get('/comments/{id}', [CommentController::class, 'index'])->name('games.comments');
   
    Route::post('/games/like', [LikesController::class, 'toggleLike'])->name('games.like.toggle');
    Route::post('/games/play', [PlayedGameController::class, 'togglePlay'])->name('games.play.toggle');
    Route::post('/games/later', [PlayLaterController::class, 'toggleLater'])->name('games.later.toggle');
    Route::post('/games/log', [LogController::class, 'storeLog'])->name('games.log.store');
    Route::post('/games/log/edit', [LogController::class, 'editLog'])->name('games.log.edit');
    Route::post('/games/log/remove', [LogController::class, 'removeLog'])->name('games.log.remove');
    Route::post('/games/comments', [CommentController::class, 'sendComment'])->name('games.log.comments');
    Route::post('/games/logLike', [LogLikesController::class, 'likeLog'])->name('games.log.like');

    Route::post('/games/comment/edit', [CommentController::class, 'editComment'])->name('games.comment.edit');
    Route::post('/games/comment/remove', [CommentController::class, 'removeComment'])->name('games.comment.remove');

    Route::resource('games', GameController::class);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
