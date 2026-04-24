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

    public function updateCategorieActivite(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'libelle_categorie' => 'required|string|max:255|unique:categorie_activite,libelle_categorie,' . $id . ',id_categorie',
            ]);

            $categorie = $this->categorieActiviteService->updateCategorieActivite($id, $validated);

            if (!$categorie) {
                return response()->json(['message' => "Catégorie non trouvée"], 404);
            }

            return response()->json([
                'message' => 'Catégorie mise à jour',
                'data' => $categorie
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation échouée', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur serveur', 'error' => $e->getMessage()], 500);
        }
    }
}