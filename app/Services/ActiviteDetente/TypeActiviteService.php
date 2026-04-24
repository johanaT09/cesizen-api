<?php

namespace App\Services\ActiviteDetente;

use App\Repositories\ActiviteDetente\TypeActiviteRepository;

class TypeActiviteService
{
    protected $typeActiviteRepository;

    public function __construct(TypeActiviteRepository $typeActiviteRepository)
    {
        $this->typeActiviteRepository = $typeActiviteRepository;
    }

    public function getAllTypes()
    {
        return $this->typeActiviteRepository->getAllTypes();
    }

    public function getTypeById($id)
    {
        return $this->typeActiviteRepository->getTypeById($id);
    }
}
