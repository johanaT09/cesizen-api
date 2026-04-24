<?php

namespace App\Http\Controllers\Informations; 

use App\Http\Controllers\Controller; 
use App\Services\Informations\CategorieActiviteService;
use Illuminate\Http\JsonResponse;
// use Symfony\Component\HttpFoundation\Request;
use Illuminate\Http\Request;


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

    public function AddCategorieActivite(Request $request): JsonResponse
{
    $validatedData = $request->validate([
        'libelle_categorie' => 'required|string|max:255|unique:categorie_activite,libelle_categorie',
    ]);
    $categorie = $this->categorieActiviteService->AddCategorieActivite($validatedData);
    return response()->json($categorie, 201);
}
}