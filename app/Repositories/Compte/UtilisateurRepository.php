<?php

namespace App\Repositories\Compte;

use App\Models\Utilisateur;

class UtilisateurRepository
{
    public function createUtilisateur($data)
    {
        return Utilisateur::create($data);
    }

    public function updateUtilisateur($id, $data)
    {
        $utilisateur = Utilisateur::find($id);
        if (!$utilisateur) {
            return null;
        }
        $utilisateur->fill($data);
        $utilisateur->save();
        return $utilisateur;
    }

    public function getAllUtilisateurs()
    {
        return Utilisateur::with('role')->get();
    }

    public function updateUtilisateurByAdmin($id, array $data)
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
