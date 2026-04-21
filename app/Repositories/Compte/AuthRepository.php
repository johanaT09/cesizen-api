<?php

namespace App\Repositories\Compte;

use App\Models\Utilisateur;

class AuthRepository
{
    public function findByEmail($email)
    {
        return Utilisateur::where('email', $email)->first();
    }
}
