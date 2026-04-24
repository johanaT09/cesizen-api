<?php

use App\Http\Controllers\Informations\CategorieActiviteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Compte\GenreUtilisateurController;
use App\Http\Controllers\Compte\UtilisateurController;
use App\Http\Controllers\Compte\AuthController;
use App\Http\Controllers\Informations\InformationController;

// Liste les genres utilisateurs
Route::get('/genres', [GenreUtilisateurController::class, 'GetGenres']);

// Création d'un compte utilisateur
Route::post('/signup', [UtilisateurController::class, 'signUp']);

// Connexion utilisateur
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Déconnexion utilisateur
    Route::post('/logout', [AuthController::class, 'logout']);
    // Modification des informations utilisateur connecté (prenom, date_naissance, id_genre)
    Route::put('/utilisateur', [UtilisateurController::class, 'update']);
});

// Liste les catégories d'informations
Route::get('/categories', [CategorieActiviteController::class, 'getAllCategories']);

// Création d'une catégorie d'information
Route::post('/addcategorie', [CategorieActiviteController::class, 'AddCategorieActivite']);

// Modification d'une catégorie d'information
Route::put('/categories/{id}', [CategorieActiviteController::class, 'updateCategorieActivite']);


// Liste toutes les informations
Route::get('/informations', [InformationController::class, 'index']);