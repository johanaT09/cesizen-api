<?php

namespace App\Http\Controllers;

use App\Services\GenreUtilisateurService;
use Illuminate\Http\Request;

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
