<?php

namespace App\Http\Controllers\Administrateur;

use App\Services\Administrateur\UtilisateurService\UtilisateurService;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Client\Request;

class UtilisateurController extends Controller
{
    protected $utilisateurService;

    public function __construct(UtilisateurService $utilisateurService)
    {
        $this->utilisateurService = $utilisateurService;
    }

    public function index(): JsonResponse
    {
        $utilisateurs = $this->utilisateurService->getAllUtilisateurs();
        return response()->json([
            'status' => 'success',
            'data' => $utilisateurs
        ]);
    }

    public function updateAdmin(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'nom_utilisateur'    => 'sometimes|string|max:255',
            'prenom_utilisateur' => 'sometimes|string|max:255',
            'email'              => 'sometimes|email|unique:utilisateur,email,' . $id . ',id_utilisateur',
            'id_role'            => 'sometimes|exists:role,id_role',
        ]);

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
}
