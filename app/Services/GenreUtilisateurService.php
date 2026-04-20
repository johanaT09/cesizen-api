<?php

namespace App\Services;

use App\Repositories\GenreUtilisateurRepository;

class GenreUtilisateurService
{
    protected $genreUtilisateurRepository;

    public function __construct(GenreUtilisateurRepository $genreUtilisateurRepository)
    {
        $this->genreUtilisateurRepository = $genreUtilisateurRepository;
    }

    public function getAllGenres()
    {
        return $this->genreUtilisateurRepository->getAllGenres();
    }
}
