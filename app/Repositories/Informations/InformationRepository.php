<?php

namespace App\Repositories\Informations;

use App\Models\Information;

class InformationRepository
{
    public function getAllInformations()
    {
        return Information::with('categorie', 'utilisateur')->get();
    }
}