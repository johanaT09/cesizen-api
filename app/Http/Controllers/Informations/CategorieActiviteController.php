<?php

namespace App\Http\Controllers\Informations; // Ajoute \Informations ici

use App\Http\Controllers\Controller; // AJOUTE CETTE LIGNE (car Controller est au parent)
use App\Services\Informations\CategorieActiviteService;
use Illuminate\Http\JsonResponse;

class CategorieActiviteController extends Controller
{
    protected $categorieActiviteService;

    public function __construct(CategorieActiviteService $categorieActiviteService)
    {
        $this->categorieActiviteService = $categorieActiviteService;
    }

    public function getAllCategories(): JsonResponse
    {
        $categories = $this->categorieActiviteService->getAllCategories();
        
        return response()->json($categories);
    }
}