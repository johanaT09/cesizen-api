<?php

namespace App\Http\Controllers\Informations;

use App\Http\Controllers\Controller;
use App\Services\Informations\InformationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    protected $informationService;

    public function __construct(InformationService $informationService)
    {
        $this->informationService = $informationService;
    }

    public function getInformations(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $informations = $this->informationService->getAllInformations($search, $categoryId);

        return response()->json([
            'status' => 'success',
            'data' => $informations
        ]);
    }

    public function getInformationById($id): JsonResponse
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

    public function getAdminInformations(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $perPage = $request->query('per_page', 20);

        $informations = $this->informationService->getAdminInformations($search, $categoryId, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => $informations
        ]);
    }

    public function createInformation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'titre_information' => 'required|string|max:255',
            'contenu_information' => 'required|string',
            'id_categorie' => 'required|integer|exists:categorie_activite,id_categorie',
        ]);

        $validated['id_utilisateur'] = $request->user()->id_utilisateur;
        $validated['date_publication_information'] = now();
        $validated['est_actif'] = true;

        $information = $this->informationService->createInformation($validated);

        return response()->json(['status' => 'success', 'data' => $information], 201);
    }

    public function updateInformation(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'titre_information' => 'required|string|max:255',
            'contenu_information' => 'required|string',
            'id_categorie' => 'required|integer|exists:categorie_activite,id_categorie',
            'est_actif' => 'required|boolean'
        ]);

        $information = $this->informationService->updateInformation($id, $validated);

        return response()->json(['status' => 'success', 'data' => $information]);
    }

    public function toggleStatus($id): JsonResponse
    {
        $information = $this->informationService->toggleStatus($id);
        return response()->json(['status' => 'success', 'data' => $information]);
    }
}
