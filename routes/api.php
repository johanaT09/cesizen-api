<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Compte\GenreUtilisateurController;
use App\Http\Controllers\Compte\UtilisateurController;
use App\Http\Controllers\Compte\AuthController;
use App\Http\Middleware\CheckTokenExpiration;
use Illuminate\Http\Request;

// Gestion des comptes :
// Liste les genres utilisateurs
Route::get('/genres', [GenreUtilisateurController::class, 'GetGenres']);

// Création d'un compte utilisateur
Route::post('/signin', [UtilisateurController::class, 'signIn']);

// Connexion utilisateur
Route::post('/login', [AuthController::class, 'login']);

// // Déconnexion utilisateur
// Route::post('/logout', [AuthController::class, 'logout']);

// // Exemple de route protégée par token et expiration
// Route::middleware(['auth:sanctum', 'check.token.expiration'])->get('/user', function (Request $request) {
// 	return $request->user();
// });

// // Déconnexion utilisateur
// Route::middleware(['auth:sanctum', 'check.token.expiration'])->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Route::middleware(['auth:sanctum'])->post('/logout', [AuthController::class, 'logout']);