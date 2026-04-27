<?php

use App\Http\Controllers\Informations\CategorieActiviteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Compte\GenreUtilisateurController;
use App\Http\Controllers\Compte\UtilisateurController;
use App\Http\Controllers\Compte\AuthController;
use App\Http\Controllers\Informations\InformationController;
use App\Http\Controllers\ActiviteDetente\TypeActiviteController;
use App\Http\Controllers\ActiviteDetente\ActiviteController;

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
    Route::put('/utilisateur', [UtilisateurController::class, 'updateUtilisateur']);
    // Route pour ajouter/retirer un favori
    Route::post('/activites/{id}/favori', [ActiviteController::class, 'toggleFavori']);
    // Route pour récupérer les favoris de l'utilisateur connecté
    Route::get('/favoris', [ActiviteController::class, 'getFavoris']);
});

// Liste les catégories d'informations
Route::get('/categories', [CategorieActiviteController::class, 'getAllCategories']);

// Création d'une catégorie d'information
Route::post('/addcategorie', [CategorieActiviteController::class, 'addCategorieActivite']);

// Modification d'une catégorie d'information
Route::put('/categories/{id}', [CategorieActiviteController::class, 'updateCategorieActivite']);


// Liste toutes les informations
Route::get('/informations', [InformationController::class, 'getInformations']);

// Récupérer une information précise par son ID
Route::get('/informations/{id}', [InformationController::class, 'getInformationById']);


// Récupérer tous les types (ex: Audio, Vidéo, Article)
Route::get('/types-activites', [TypeActiviteController::class, 'getTypesActivites']);

// Récupérer un type spécifique
Route::get('/types-activites/{id}', [TypeActiviteController::class, 'getTypeActiviteById']);

// Ajout d'un type d'activité
Route::post('/types-activites', [TypeActiviteController::class, 'createType']);

// Liste toutes les activités de détente
Route::get('/activites', [ActiviteController::class, 'getAllActivites']);

// Récupérer une activité de détente par son ID
Route::get('/activites/{id}', [ActiviteController::class, 'getActiviteById']);

// Liste les activités filtrées par l'ID du type
Route::get('/activites/type/{typeId}', [ActiviteController::class, 'getActivitesByType']);

// Liste les activités filtrées par l'ID de la catégorie
Route::get('/activites/categorie/{categorieId}', [ActiviteController::class, 'getActivitesByCategorie']);

// Ajouter une activité
Route::post('/activite', [ActiviteController::class, 'addActivite']);

// Modifier une activité
Route::put('/activite/{id}', [ActiviteController::class, 'update']);
