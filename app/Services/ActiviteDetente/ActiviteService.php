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
}