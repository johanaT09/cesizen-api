<?php

namespace App\Repositories\Compte;

use App\Models\Utilisateur;
use App\Models\Role;

class UtilisateurRepository
{
    public function createUtilisateur($data)
    {
        return Utilisateur::create($data);
    }

    public function findById($id)
    {
        return Utilisateur::with(['genre', 'role']) // Adaptez les noms des relations selon votre modèle
            ->find($id);
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

    public function find($id)
    {
        return Utilisateur::where('id_utilisateur', $id)->first();
    }

    public function updateAnonymization(int $userId, array $data)
    {
        return Utilisateur::where('id_utilisateur', $userId)->update($data);
    }

    public function getAllUtilisateurs($perPage, $search, $roleId, $status)
    {
        $query = Utilisateur::with(['role', 'genre']);

        if (!empty($search)) {
            $query->where('email', 'like', '%' . $search . '%');
        }

        if (!empty($roleId)) {
            $query->where('id_role', $roleId);
        }
        if ($status === 'actif') {
            $query->where('est_actif', true);
        } elseif ($status === 'desactive') {
            $query->where('est_actif', false)->whereNull('date_anonymisation');
        } elseif ($status === 'anonymise') {
            $query->whereNotNull('date_anonymisation');
        }

        return $query->paginate($perPage);
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

    public function createUtilisateurByAdmin($data)
    {
        return Utilisateur::create($data);
    }

    public function getAllRoles()
    {
        return Role::all();
    }
}
