<?php

namespace App\Http\Controllers\Compte;

use App\Http\Controllers\Controller;
use App\Services\Compte\GenreUtilisateurService;

class GenreUtilisateurController extends Controller
{
    protected $genreUtilisateurService;

    public function __construct(GenreUtilisateurService $genreUtilisateurService)
    {
        $this->genreUtilisateurService = $genreUtilisateurService;
    }

    public function GetGenres()
    {
        $genres = $this->genreUtilisateurService->getAllGenres();

        return response()->json($genres);
    }
}
