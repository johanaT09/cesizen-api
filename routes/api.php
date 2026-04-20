<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Compte\GenreUtilisateurController;
use App\Http\Controllers\Compte\UtilisateurController;

// Gestion des comptes :

// Liste les genres utilisateurs
Route::get('/genres', [GenreUtilisateurController::class, 'GetGenres']);

// Création d'un compte utilisateur
Route::post('/signin', [UtilisateurController::class, 'signIn']);

