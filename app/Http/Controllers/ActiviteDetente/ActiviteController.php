<?php

namespace App\Http\Controllers\ActiviteDetente;

use App\Http\Controllers\Controller;
use App\Services\ActiviteDetente\ActiviteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    protected $activiteService;

    public function __construct(ActiviteService $activiteService)
    {
        $this->activiteService = $activiteService;
    }

    public function getAllActivites(): JsonResponse
    {
        $activites = $this->activiteService->getAllActivites();

        return response()->json([
            'status' => 'success',
            'data' => $activites
        ]);
    }

    public function getActiviteById($id): JsonResponse
    {
        $activite = $this->activiteService->getActiviteById($id);

        if (!$activite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Activité non trouvée'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activite
        ]);
    }

    public function getActivitesByType($typeId): JsonResponse
    {
        $activites = $this->activiteService->getActivitesByType($typeId);

        if ($activites->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Aucune activité trouvée pour ce type',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activites
        ]);
    }

    public function getActivitesByCategorie($categorieId): JsonResponse
    {
        $activites = $this->activiteService->getActivitesByCategorie($categorieId);

        if ($activites->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Aucune activité trouvée pour cette catégorie',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activites
        ]);
    }

    public function addActivite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'titre_activite' => 'required|string|max:255',
            'description_activite' => 'nullable|string',
            'lien_ressource' => 'required|string', // URL ou contenu
            'id_type' => 'required|exists:type,id_type', // Vérifie l'existence dans la table type
            'id_categorie' => 'required|exists:categorie_activite,id_categorie', // Vérifie dans la table categorie
        ]);

        $activite = $this->activiteService->createActivite($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Activité ajoutée avec succès',
            'data' => $activite
        ], 201);
    }
}
