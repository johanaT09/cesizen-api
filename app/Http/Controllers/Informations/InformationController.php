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
}