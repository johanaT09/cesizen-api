<?php

namespace App\Http\Controllers\Compte;

use Illuminate\Http\Request;
use App\Services\Compte\UtilisateurService;
use App\Http\Controllers\Controller;

class UtilisateurController extends Controller
{
    protected $utilisateurService;

    public function __construct(UtilisateurService $utilisateurService)
    {
        $this->utilisateurService = $utilisateurService;
    }

    public function signUp(Request $request)
    {
        try {
            if ($request->header('Content-Type') !== 'application/json' && !$request->isJson()) {
                return response()->json(['message' => 'Requête mal formée (JSON attendu)'], 400);
            }

            $validated = $request->validate([
                'prenom' => 'required|string',
                'date_naissance' => 'required|date',
                'id_genre' => 'required|exists:genre_utilisateur,id_genre',
                'email' => 'required|email|unique:utilisateur,email',
                'mot_de_passe' => 'required|string|min:6',
                'consentement_rgpd' => 'required|boolean',
            ]);

            $utilisateur = $this->utilisateurService->signUp($validated);
            return response()->json(['message' => 'Création compte réussie'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Champs requis',
                'errors' => $e->errors()
            ], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\BadRequestHttpException $e) {
            return response()->json(['message' => 'Requête mal formée'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    
    public function update(Request $request)
    {
        try {
            if ($request->header('Content-Type') !== 'application/json' && !$request->isJson()) {
                return response()->json(['message' => 'Requête mal formée (JSON attendu)'], 400);
            }

            $validated = $request->validate([
                'prenom' => 'sometimes|required|string',
                'date_naissance' => 'sometimes|required|date',
                'id_genre' => 'sometimes|required|exists:genre_utilisateur,id_genre',
            ]);

            $user = $request->user();
            $utilisateur = $this->utilisateurService->updateUtilisateur($user->id_utilisateur, $validated);
            if (!$utilisateur) {
                return response()->json(['message' => "Utilisateur non trouvé"], 404);
            }
            return response()->json(['message' => 'Utilisateur mis à jour', 'utilisateur' => $utilisateur], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Champs requis',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }
}
