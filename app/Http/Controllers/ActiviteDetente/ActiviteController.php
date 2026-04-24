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
}
