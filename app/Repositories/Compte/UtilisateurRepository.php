<?php

namespace App\Repositories\Compte;

use App\Models\Utilisateur;

class UtilisateurRepository
{
    public function createUtilisateur($data)
    {
        return Utilisateur::create($data);
    }
}
