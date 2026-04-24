<?php

namespace App\Http\Controllers\ActiviteDetente;

use App\Http\Controllers\Controller;
use App\Services\ActiviteDetente\ActiviteService;
use Illuminate\Http\JsonResponse;

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
}
