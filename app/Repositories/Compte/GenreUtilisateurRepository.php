<?php

namespace App\Repositories\Compte;

use App\Models\GenreUtilisateur;

class GenreUtilisateurRepository
{
    public function getAllGenres()
    {
        return GenreUtilisateur::all();
    }
}
