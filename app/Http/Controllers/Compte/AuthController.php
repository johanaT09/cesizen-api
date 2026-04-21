<?php

namespace App\Http\Controllers\Compte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Compte\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'mot_de_passe' => 'required|string',
            ]);
            $result = $this->authService->login($validated['email'], $validated['mot_de_passe']);
            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $result['user'],
                'token' => $result['token']
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Champs requis',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request)
{
    $this->authService->logout($request->user());

    return response()->json([
        'message' => 'Déconnexion réussie. Token supprimé.'
    ], 200);
}
}
