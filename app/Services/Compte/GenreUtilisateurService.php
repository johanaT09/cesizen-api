<?php

namespace App\Services\Compte;

use App\Repositories\Compte\GenreUtilisateurRepository;

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
