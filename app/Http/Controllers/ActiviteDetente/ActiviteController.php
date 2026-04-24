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
}