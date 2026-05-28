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

    public function addCategorieActivite(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'libelle_categorie' => 'required|string|max:255|unique:categorie_activite,libelle_categorie',
        ], [
            'libelle_categorie.unique' => 'Ce nom de catégorie est déjà utilisé.',
            'libelle_categorie.required' => 'Le nom de la catégorie est obligatoire.',
        ]);

        $categorie = $this->categorieActiviteService->AddCategorieActivite($validatedData);
        return response()->json($categorie, 201);
    }

    public function updateCategorieActivite(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'libelle_categorie' => 'required|string|max:255|unique:categorie_activite,libelle_categorie,' . $id . ',id_categorie',
            ], [
                'libelle_categorie.unique' => 'Ce nom de catégorie est déjà utilisé.',
                'libelle_categorie.required' => 'Le nom de la catégorie est obligatoire.',
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

    public function deleteCategorieActivite($id): JsonResponse
    {
        try {
            $status = $this->categorieActiviteService->deleteCategorieActivite($id);

            if ($status === 'NOT_FOUND') {
                return response()->json(['message' => "Catégorie non trouvée"], 404);
            }

            if ($status === 'HAS_RELATIONS') {
                return response()->json([
                    'message' => "Impossible de supprimer : cette catégorie est liée à des articles d'information actifs."
                ], 422);
            }

            return response()->json([
                'message' => 'Catégorie supprimée avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur serveur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
