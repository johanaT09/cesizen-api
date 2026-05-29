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

    public function getAllActivites($search = null, $catId = null, $typeId = null)
    {
        return $this->activiteRepository->getAllActivites($search, $catId, $typeId);
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

    public function getAdminActivites($search = null, $catId = null, $typeId = null, $perPage = 20)
    {
        return $this->activiteRepository->getAdminActivites($search, $catId, $typeId, $perPage);
    }

   public function getProgression($userId, $activiteId): int
    {
        $session = $this->activiteRepository->findSession($userId, $activiteId);

        return $session ? (int)$session->duree_realisee : 0;
    }

    public function saveProgression($userId, $activiteId, $progression, $estTermine = false): void
    {
        $this->activiteRepository->updateOrCreateSession($userId, $activiteId, $progression, $estTermine);
    }

    public function getStartedActivities($userId)
    {
        return $this->activiteRepository->getStartedActivities($userId);
    }
}
