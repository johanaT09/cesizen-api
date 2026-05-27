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
                'prenom'            => 'required|string|max:255',
                'date_naissance'    => 'required|date',
                'id_genre'          => 'required|exists:genre_utilisateur,id_genre',
                'email'             => 'required|email|unique:utilisateur,email',
                'mot_de_passe'      => 'required|string|min:6',
                'consentement_rgpd' => 'required|boolean',
            ]);

            $utilisateur = $this->utilisateurService->signUp($validated);

            $token = $utilisateur->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Création de compte réussie',
                'token'   => $token,
                'user'    => $utilisateur 
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            if (isset($errors['email'])) {
                $errors['email'][0] = "Cette adresse email est déjà associée à un compte.";
            }
            return response()->json([
                'message' => 'Données invalides',
                'errors'  => $errors
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    public function getMonProfil(Request $request)
    {
        $utilisateur = $this->utilisateurService->getUtilisateurById($request->user()->id_utilisateur);

        return response()->json([
            'prenom' => $utilisateur->prenom,
            'email' => $utilisateur->email,
            'date_naissance' => $utilisateur->date_naissance,
            'id_genre' => $utilisateur->id_genre,
            'libelle_genre' => $utilisateur->genre ? $utilisateur->genre->libelle_genre : null // Le libellé ici
        ]);
    }


    public function updateUtilisateur(Request $request)
    {
        try {
            if ($request->header('Content-Type') !== 'application/json' && !$request->isJson()) {
                return response()->json(['message' => 'Requête mal formée (JSON attendu)'], 400);
            }

            $validated = $request->validate([
                'prenom'                    => 'sometimes|required|string|max:255',
                'date_naissance'            => 'sometimes|required|date',
                'id_genre'                  => 'sometimes|required|exists:genre_utilisateur,id_genre',
                'current_password'          => 'nullable|string',
                'new_password'              => 'nullable|string|min:8|required_with:current_password',
                'new_password_confirmation' => 'nullable|string|required_with:new_password|same:new_password',
            ], [
                'new_password.required_with'   => 'Le nouveau mot de passe est obligatoire pour changer de mot de passe.',
                'new_password_confirmation.same' => 'La confirmation du nouveau mot de passe ne correspond pas.',
            ]);

            $user = $request->user();

            $utilisateur = $this->utilisateurService->updateUtilisateur($user->id_utilisateur, $validated);

            if (!$utilisateur) {
                return response()->json(['message' => "Utilisateur non trouvé"], 404);
            }

            return response()->json(['message' => 'Utilisateur mis à jour avec succès', 'utilisateur' => $utilisateur], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Champs requis ou invalides',
                'errors'  => $e->errors()
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    public function supprimerUtilisateurByUser(Request $request)
    {
        $user = $request->user();

        try {
            $this->utilisateurService->anonymiserCompte($user->id_utilisateur);
            return response()->json(['message' => 'Compte anonymisé avec succès.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du traitement.'], 500);
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
