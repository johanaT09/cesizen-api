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
}
