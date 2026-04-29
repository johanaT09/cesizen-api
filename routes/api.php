<?php

use App\Http\Controllers\Informations\CategorieActiviteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Compte\GenreUtilisateurController;
use App\Http\Controllers\Compte\UtilisateurController;
use App\Http\Controllers\Compte\AuthController;
use App\Http\Controllers\Informations\InformationController;
use App\Http\Controllers\ActiviteDetente\TypeActiviteController;
use App\Http\Controllers\ActiviteDetente\ActiviteController;


// Route accessible sans authentification

// Gestion du compte utilisateur : 
Route::post('/signup', [UtilisateurController::class, 'signUp']); // Création d'un compte utilisateur
Route::get('/genres', [GenreUtilisateurController::class, 'GetGenres']); // Liste les genres utilisateurs
Route::post('/login', [AuthController::class, 'login']); // Connexion utilisateur

// Informations : 
Route::get('/informations', [InformationController::class, 'getInformations']); // Liste toutes les informations
Route::get('/informations/{id}', [InformationController::class, 'getInformationById']); // Récupérer une information précise par son ID

// Catégories (Informations et Activités de détente) :
Route::get('/categories', [CategorieActiviteController::class, 'getAllCategories']); // Liste les catégories d'informations

// Activités de détente :
Route::get('/types-activites', [TypeActiviteController::class, 'getTypesActivites']); // Récupérer tous les types (ex: Audio, Vidéo, Article)
Route::get('/types-activites/{id}', [TypeActiviteController::class, 'getTypeActiviteById']); // Récupérer un type spécifique
Route::get('/activites', [ActiviteController::class, 'getAllActivites']); // Liste toutes les activités de détente
Route::get('/activites/{id}', [ActiviteController::class, 'getActiviteById']); // Récupérer une activité de détente par son ID
Route::get('/activites/type/{typeId}', [ActiviteController::class, 'getActivitesByType']); // Liste les activités filtrées par l'ID du type
Route::get('/activites/categorie/{categorieId}', [ActiviteController::class, 'getActivitesByCategorie']); // Liste les activités filtrées par l'ID de la catégorie



// Routes accessibles uniquement aux utilisateurs authentifiés
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);  // Déconnexion utilisateur
    Route::put('/utilisateur', [UtilisateurController::class, 'updateUtilisateur']); // Modification des informations utilisateur connecté (prenom, date_naissance, id_genre)
    Route::post('/activites/{id}/favori', [ActiviteController::class, 'toggleFavori']); // Route pour ajouter/retirer un favori
    Route::get('/favoris', [ActiviteController::class, 'getFavoris']); // Route pour récupérer les favoris de l'utilisateur connecté

    // Routes accessibles uniquement aux administrateurs
    Route::middleware('admin')->group(function () {
        Route::post('/addcategorie', [CategorieActiviteController::class, 'addCategorieActivite']); // Création d'une catégorie d'information
        Route::put('/categories/{id}', [CategorieActiviteController::class, 'updateCategorieActivite']); // Modification d'une catégorie d'information
        Route::post('/types-activites', [TypeActiviteController::class, 'createType']); // Ajout d'un type d'activité
        Route::post('/activite', [ActiviteController::class, 'addActivite']); // Ajouter une activité
        Route::patch('/activite/{id}/desactiver', [ActiviteController::class, 'disableActivite']); // Désactiver une activité
        Route::put('/activite/{id}', [ActiviteController::class, 'update']); // Modifier une activité
        Route::get('/utilisateurs', [UtilisateurController::class, 'getUtilisateursComptes']); // Récupérer la liste de tous les utilisateurs
        Route::put('/utilisateur/{id}', [UtilisateurController::class, 'updateUtilisateurByAdmin']); // Modifier les informations d'un utilisateur (admin)
        Route::post('/utilisateur/admin-create', [UtilisateurController::class, 'createUtilisateurByAdmin']);
    }); 
});
