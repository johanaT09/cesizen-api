<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GenreUtilisateurController;

// GET /api/genres => GenreUtilisateurController@GetGenres
Route::get('/genres', [GenreUtilisateurController::class, 'GetGenres']);
