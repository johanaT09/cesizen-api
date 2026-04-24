<?php

namespace App\Repositories\Informations;

use App\Models\CategorieActivite;

class CategorieActiviteRepository
{
    // Dans CategorieActiviteRepository.php
    public function getAllCategories()
    {
        // Attention : ne retourne qu'une liste de libellés sans ID
        // return CategorieActivite::select('id_categorie', 'libelle_categorie')->get();
        return CategorieActivite::all(['libelle_categorie']);
    }
}
