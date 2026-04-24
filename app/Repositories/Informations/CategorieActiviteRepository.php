<?php

namespace App\Repositories\Informations;

use App\Models\CategorieActivite;

class CategorieActiviteRepository
{
    public function getAllCategories()
    {
        return CategorieActivite::all(['libelle_categorie']);
    }
}
