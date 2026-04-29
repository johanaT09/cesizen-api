<?php

namespace App\Services\Administrateur\UtilisateurService;


class UtilisateurService
{
    protected $utilisateurRepository;

    public function getAllUtilisateurs()
    {
        return $this->utilisateurRepository->getAllUtilisateurs();
    }

    public function updateUtilisateur($id, array $data)
    {
        return $this->utilisateurRepository->updateUtilisateur($id, $data);
    }
}
