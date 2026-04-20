<?php

namespace App\Repositories;

use App\Models\GenreUtilisateur;

class GenreUtilisateurRepository
{
    public function getAllGenres()
    {
        // Récupère tous les genres (libellé)
        return GenreUtilisateur::all(['libelle_genre']);
    }
}
