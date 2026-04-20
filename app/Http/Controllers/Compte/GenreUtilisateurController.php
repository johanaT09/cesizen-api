<?php

namespace App\Http\Controllers\Compte;

use App\Services\Compte\GenreUtilisateurService;
use App\Http\Controllers\Controller;

class GenreUtilisateurController extends Controller
{
    protected $genreUtilisateurService;

    public function __construct(GenreUtilisateurService $genreUtilisateurService)
    {
        $this->genreUtilisateurService = $genreUtilisateurService;
    }

    // GET /genres
    public function GetGenres()
    {
        $genres = $this->genreUtilisateurService->getAllGenres();
        return response()->json($genres);
    }
}
