<?php

namespace App\Repositories\ActiviteDetente;

use App\Models\ActiviteDetente;
use App\Models\Utilisateur;

class ActiviteRepository
{
    public function getAllActivites()
    {
        return ActiviteDetente::with(['categorie', 'type'])->get();
    }

    public function getActiviteById($id)
    {
        return ActiviteDetente::with(['categorie', 'type'])->find($id);
    }

    public function getActivitesByType($typeId)
    {
        return ActiviteDetente::with(['categorie', 'type'])
            ->where('id_type', $typeId)
            ->get();
    }

    public function getActivitesByCategorie($categorieId)
    {
        return ActiviteDetente::with(['categorie', 'type'])
            ->where('id_categorie', $categorieId)
            ->get();
    }

    public function createActivite(array $data)
    {
        $data['est_actif'] = true;
        return ActiviteDetente::create($data);
    }

    public function toggleFavori($userId, $activiteId)
    {
        $utilisateur = \App\Models\Utilisateur::find($userId);
        if (!$utilisateur) {
            return null;
        }
        return $utilisateur->activitesFavoris()->toggle($activiteId);
    }

    public function getFavorisByUtilisateur($userId)
    {
        $utilisateur = Utilisateur::find($userId);

        return $utilisateur->activitesFavoris()->with(['categorie', 'type'])->get();
    }
}
