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

    public function signIn($data)
    {
        $data['est_actif'] = true;
        $data['date_anonymisation'] = now();
        $role = Role::where('libelle', 'Utilisateur')->first();
        $data['id_role'] = $role ? $role->id_role : null;
        $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);
        unset($data['mot_de_passe']);
        return $this->utilisateurRepository->createUtilisateur($data);
    }
}
