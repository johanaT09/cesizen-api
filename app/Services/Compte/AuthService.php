<?php

namespace App\Services\Compte;

use App\Repositories\Compte\AuthRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login($email, $mot_de_passe)
    {
        $user = $this->authRepository->findByEmail($email);
        if (!$user) {
            throw new \Exception('Identifiants invalides');
        }
        if (!Hash::check($mot_de_passe, $user->mot_de_passe)) {
            throw new \Exception('Identifiants invalides');
        }

        // Générer un token Sanctum valable 7h
        $tokenResult = $user->createToken('auth_token');
        $token = $tokenResult->plainTextToken;
        $tokenModel = $tokenResult->accessToken;
        $tokenModel->expires_at = now()->addHours(7);
        $tokenModel->save();
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout($user)
    {
        return $user->currentAccessToken()->delete();
    }
}
