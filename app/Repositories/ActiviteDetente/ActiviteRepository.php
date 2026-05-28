<?php

namespace App\Repositories\ActiviteDetente;

use App\Models\ActiviteDetente;
use App\Models\Utilisateur;

class ActiviteRepository
{
    public function getAllActivites($search = null, $catId = null, $typeId = null)
    {
        $query = ActiviteDetente::with(['categorie', 'type'])
            ->where('est_actif', true);

        if ($search !== null && $search !== '') {
            $query->where('titre_activite', 'ILIKE', '%' . $search . '%');
        }

        if ($catId) {
            $query->where('id_categorie', $catId);
        }

        if ($typeId) {
            $query->where('id_type', $typeId);
        }

        return $query->orderBy('id_activite', 'asc')->paginate(9);
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

    public function updateActivite($id, array $data)
    {
        $activite = ActiviteDetente::find($id);

        if (!$activite) {
            return null;
        }

        $activite->fill($data);
        $activite->save();

        return $activite;
    }

    public function disableActivite($id)
    {
        $activite = ActiviteDetente::find($id);

        if (!$activite) {
            return null;
        }

        $activite->est_actif = false;
        $activite->save();

        return $activite;
    }

    public function getAdminActivites($search = null, $catId = null, $typeId = null, $perPage = 20)
    {
        $query = ActiviteDetente::with(['categorie', 'type']);

        if ($search !== null && $search !== '') {
            $query->where('titre_activite', 'ILIKE', '%' . $search . '%');
        }

        if ($catId) {
            $query->where('id_categorie', $catId);
        }

        if ($typeId) {
            $query->where('id_type', $typeId);
        }

        return $query->orderBy('id_activite', 'asc')->paginate($perPage);
    }
}
