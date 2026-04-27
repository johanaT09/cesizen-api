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

    public function createActivite(array $data)
    {
        return $this->activiteRepository->createActivite($data);
    }

    public function toggleFavori($userId, $activiteId)
    {
        return $this->activiteRepository->toggleFavori($userId, $activiteId);
    }

    public function getFavorisByUtilisateur($userId)
    {
        return $this->activiteRepository->getFavorisByUtilisateur($userId);
    }

    public function updateActivite($id, array $data)
    {
        return $this->activiteRepository->updateActivite($id, $data);
    }

    public function desactiverActivite($id)
    {
        return $this->activiteRepository->disableActivite($id);
    }
}
