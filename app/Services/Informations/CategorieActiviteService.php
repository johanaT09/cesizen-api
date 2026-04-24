<?php

namespace App\Services\Informations;

use App\Repositories\Informations\CategorieActiviteRepository;

class CategorieActiviteService
{
    protected $categorieActiviteRepository;

    public function __construct(CategorieActiviteRepository $categorieActiviteRepository)
    {
        $this->categorieActiviteRepository = $categorieActiviteRepository;
    }

    public function getAllCategories()
    {
        return $this->categorieActiviteRepository->getAllCategories();
    }
    
    public function AddCategorieActivite($data)
    {
        return $this->categorieActiviteRepository->AddCategorieActivite($data);
    }
}
