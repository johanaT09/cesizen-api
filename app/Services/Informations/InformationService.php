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

    public function getAllInformations()
    {
        return $this->informationRepository->getAllInformations();
    }

    public function getInformationById($id)
    {
        return $this->informationRepository->getInformationById($id);
    }
}
