<?php

namespace App\Services\Compte;

use App\Repositories\Compte\UtilisateurRepository;
use App\Models\Role;

class UtilisateurService
{
    protected $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function signUp($data)
    {
        $data['est_actif'] = true;
        $data['date_anonymisation'] = null;
        $data['consentement_rgpd'] = now();
        $role = Role::where('libelle', 'Utilisateur')->first();
        $data['id_role'] = $role ? $role->id_role : null;
        $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);
        return $this->utilisateurRepository->createUtilisateur($data);
    }

    public function updateUtilisateur($id, $data)
    {
        return $this->utilisateurRepository->updateUtilisateur($id, $data);
    }

    public function getAllUtilisateurs()
    {
        return $this->utilisateurRepository->getAllUtilisateurs();
    }

    public function updateUtilisateurByAdmin($id, array $data)
    {
        return $this->utilisateurRepository->updateUtilisateur($id, $data);
    }

    public function createUtilisateurByAdmin($data)
    {
        return $this->utilisateurRepository->createUtilisateurByAdmin($data);
    }
}
