<?php

namespace App\Http\Controllers\Compte;

use Illuminate\Http\Request;
use App\Services\Compte\UtilisateurService;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

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


    public function updateUtilisateur(Request $request)
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

    public function getUtilisateursComptes(): JsonResponse
    {
        $utilisateurs = $this->utilisateurService->getAllUtilisateurs();
        return response()->json([
            'status' => 'success',
            'data' => $utilisateurs
        ]);
    }

    public function updateUtilisateurByAdmin(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'prenom'         => 'sometimes|string|max:255',
            'date_naissance' => 'sometimes|date',
            'id_genre'       => 'sometimes|exists:genre_utilisateur,id_genre',
            'mot_de_passe'   => 'sometimes|string|min:6',
            'est_actif'      => 'sometimes|boolean',
            'email'          => 'sometimes|email|unique:utilisateur,email,' . $id . ',id_utilisateur',
            'id_role'        => 'sometimes|exists:role,id_role',
        ]);

        if (isset($validated['mot_de_passe'])) {
            $validated['mot_de_passe'] = bcrypt($validated['mot_de_passe']);
        }

        $utilisateur = $this->utilisateurService->updateUtilisateur($id, $validated);

        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Compte utilisateur mis à jour',
            'data' => $utilisateur
        ]);
    }

    public function createUtilisateurByAdmin(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'prenom'            => 'required|string|max:255',
                'email'             => 'required|email|unique:utilisateur,email',
                'mot_de_passe'      => 'required|string|min:6',
                'date_naissance'    => 'required|date',
                'id_genre'          => 'required|exists:genre_utilisateur,id_genre',
                'id_role'           => 'required|exists:role,id_role',
                'est_actif'         => 'sometimes|boolean',
            ]);

            // Préparation des données pour le service
            $data = $validated;
            $data['mot_de_passe'] = bcrypt($validated['mot_de_passe']);
            $data['consentement_rgpd'] = now(); // On valide par défaut
            $data['est_actif'] = $validated['est_actif'] ?? true;

            $utilisateur = $this->utilisateurService->createUtilisateurByAdmin($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Compte créé avec succès par l\'administrateur',
                'data' => $utilisateur
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function desactiverUtilisateurByAdmin(Request $request, $id)
    {
        $utilisateur = $this->utilisateurService->desactiverUtilisateurByAdmin($id);
        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }
        return response()->json(['message' => 'Compte désactivé avec succès'], 200);
    }

    public function supprimerUtilisateurByAdmin(Request $request, $id)
    {
        $utilisateur = $this->utilisateurService->anonymiserUtilisateurByAdmin($id);
        if (!$utilisateur) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }
        return response()->json(['message' => 'Compte anonymisé et supprimé (RGPD)'], 200);
    }
}
