<?php

namespace App\Repositories\Informations;

use App\Models\CategorieActivite;

class CategorieActiviteRepository
{
    public function getAllCategories()
    {
        return CategorieActivite::all(['libelle_categorie']);
    }

    public function AddCategorieActivite(array $data)
    {
        return CategorieActivite::create($data);
    }
}
