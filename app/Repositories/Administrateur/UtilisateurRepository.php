<?php

namespace App\Repositories\Administrateur;

use App\Models\Utilisateur;

class UtilisateurRepository
{
    public function getAllUtilisateurs()
    {
        return Utilisateur::with('role')->get();
    }

    public function updateUtilisateur($id, array $data)
    {
        $utilisateur = Utilisateur::find($id);
        if (!$utilisateur) {
            return null;
        }

        $utilisateur->fill($data);
        $utilisateur->save();

        return $utilisateur;
    }
}
