<?php

namespace App\Services\ActiviteDetente;

use App\Repositories\ActiviteDetente\ActiviteRepository;

class ActiviteService
{
    protected $activiteRepository;

    public function __construct(ActiviteRepository $activiteRepository)
    {
        $this->activiteRepository = $activiteRepository;
    }

    public function getAllActivites()
    {
        return $this->activiteRepository->getAllActivites();
    }

    public function getActiviteById($id)
    {
        return $this->activiteRepository->getActiviteById($id);
    }

    public function getActivitesByType($typeId)
    {
        return $this->activiteRepository->getActivitesByType($typeId);
    }

    public function getActivitesByCategorie($categorieId)
    {
        return $this->activiteRepository->getActivitesByCategorie($categorieId);
    }
}
