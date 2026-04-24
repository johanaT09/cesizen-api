<?php

namespace App\Http\Controllers\Informations; 

use App\Http\Controllers\Controller; 
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