<?php

namespace App\Http\Controllers\Informations;

use App\Http\Controllers\Controller;
use App\Services\Informations\InformationService;
use Illuminate\Http\JsonResponse;

class InformationController extends Controller
{
    protected $informationService;

    public function __construct(InformationService $informationService)
    {
        $this->informationService = $informationService;
    }

    public function index(): JsonResponse
    {
        $informations = $this->informationService->getAllInformations();

        return response()->json([
            'status' => 'success',
            'data' => $informations
        ]);
    }

    public function show($id): JsonResponse
    {
        $information = $this->informationService->getInformationById($id);

        if (!$information) {
            return response()->json([
                'status' => 'error',
                'message' => 'Information non trouvée'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $information
        ]);
    }
}
