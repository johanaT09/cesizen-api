<?php

namespace App\Services\Compte;

use App\Repositories\Compte\UtilisateurRepository;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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

    public function getUtilisateurById($id)
    {
        return $this->utilisateurRepository->findById($id);
    }

    public function updateUtilisateur($id, $data)
    {
        $user = $this->utilisateurRepository->findById($id);
        if (!$user) {
            return null;
        }

        if (!empty($data['current_password'])) {
            if (!Hash::check($data['current_password'], $user->mot_de_passe)) {
                throw new \InvalidArgumentException('Votre mot de passe actuel est incorrect.');
            }

            $data['mot_de_passe'] = bcrypt($data['new_password']);
        }

        unset($data['current_password'], $data['new_password'], $data['new_password_confirmation']);

        return $this->utilisateurRepository->updateUtilisateur($id, $data);
    }

    public function anonymiserCompte(int $userId)
    {
        $data = [
            'prenom' => 'Anonyme',
            'email' => 'supprime_' . $userId . '_' . time() . '@domaine.com',
            'mot_de_passe' => Hash::make(Str::random(60)),
            'est_actif' => false,
            'date_anonymisation' => now(),
        ];

        return $this->utilisateurRepository->updateAnonymization($userId, $data);
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

    public function desactiverUtilisateurByAdmin($id)
    {
        return $this->utilisateurRepository->updateUtilisateurByAdmin($id, [
            'est_actif' => false
        ]);
    }

    public function anonymiserUtilisateurByAdmin($id)
    {
        return $this->utilisateurRepository->updateUtilisateurByAdmin($id, [
            'prenom'             => 'Anonyme',
            'email'              => 'anonyme_' . $id . '_' . uniqid() . '@cesizen.fr',
            'mot_de_passe'       => bcrypt(str::random(16)), // Mot de passe aléatoire inutilisable
            'est_actif'          => false,
            'date_anonymisation' => now(),
        ]);
    }
}
