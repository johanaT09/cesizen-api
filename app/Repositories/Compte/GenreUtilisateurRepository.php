<?php

namespace App\Repositories\Compte;

use App\Models\GenreUtilisateur;

class GenreUtilisateurRepository
{
    public function getAllGenres()
    {
        // Récupère tous les genres (libellé)
        return GenreUtilisateur::all(['libelle_genre']);
    }
}
