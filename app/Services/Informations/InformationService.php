<?php

namespace App\Services\Informations;

use App\Repositories\Informations\InformationRepository;

class InformationService
{
    protected $informationRepository;

    public function __construct(InformationRepository $informationRepository)
    {
        $this->informationRepository = $informationRepository;
    }

    public function getAllInformations($search = null, $categoryId = null)
    {
        return $this->informationRepository->getAllInformations($search, $categoryId);
    }
    public function getInformationById($id)
    {
        return $this->informationRepository->getInformationById($id);
    }

    public function getAdminInformations($search = null, $categoryId = null, $perPage = 20)
    {
        return $this->informationRepository->getAdminInformations($search, $categoryId, $perPage);
    }

    public function createInformation(array $data)
    {
        return $this->informationRepository->create($data);
    }

    public function updateInformation($id, array $data)
    {
        return $this->informationRepository->update($id, $data);
    }

    public function toggleStatus($id)
    {
        return $this->informationRepository->toggleStatus($id);
    }
}
