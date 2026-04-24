<?php

namespace App\Http\Controllers\ActiviteDetente;

use App\Http\Controllers\Controller;
use App\Services\ActiviteDetente\TypeActiviteService;
use Illuminate\Http\JsonResponse;

class TypeActiviteController extends Controller
{
    protected $typeActiviteService;

    public function __construct(TypeActiviteService $typeActiviteService)
    {
        $this->typeActiviteService = $typeActiviteService;
    }

    public function getTypesActivites(): JsonResponse
    {
        return response()->json($this->typeActiviteService->getAllTypes());
    }

    public function getTypeActiviteById($id): JsonResponse
    {
        $type = $this->typeActiviteService->getTypeById($id);
        
        if (!$type) {
            return response()->json(['message' => 'Type non trouvé'], 404);
        }

        return response()->json($type);
    }
}
