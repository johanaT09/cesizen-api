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

    public function updateCategorieActivite($id, $data)
{
    $categorie = CategorieActivite::find($id);
    
    if (!$categorie) {
        return null;
    }

    $categorie->update($data); 
    return $categorie;
}
}
