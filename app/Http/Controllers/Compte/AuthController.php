<?php

namespace App\Http\Controllers\Compte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Compte\AuthService;
use Illuminate\Validation\ValidationException;

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
            $user = $result['user'];

            return response()->json([
                'message' => 'Connexion réussie',
                'token' => $result['token'],
                'user' => [
                    'id_utilisateur' => $user->id_utilisateur,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'id_role' => (int) $user->id_role,
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Données de connexion manquantes ou mal formatées.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getmessage() ?: 'Identifiants incorrects.'
            ], 401);
        }
    }
}
