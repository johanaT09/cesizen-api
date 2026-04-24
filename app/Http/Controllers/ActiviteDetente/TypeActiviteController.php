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

    public function index(): JsonResponse
    {
        return response()->json($this->typeActiviteService->getAllTypes());
    }
}
