<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Compte\GenreUtilisateurController;
use App\Http\Controllers\Compte\UtilisateurController;
use App\Http\Controllers\Compte\AuthController;

// Liste les genres utilisateurs
Route::get('/genres', [GenreUtilisateurController::class, 'GetGenres']);

// Création d'un compte utilisateur
Route::post('/signin', [UtilisateurController::class, 'signIn']);

// Connexion utilisateur
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Déconnexion utilisateur
    Route::post('/logout', [AuthController::class, 'logout']);
    // Modification des informations utilisateur connecté (prenom, date_naissance, id_genre)
    Route::put('/utilisateur', [UtilisateurController::class, 'update']);
});
