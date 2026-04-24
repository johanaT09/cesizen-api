<?php

namespace App\Repositories\ActiviteDetente;

use App\Models\ActiviteDetente;

class ActiviteRepository
{
    public function getAllActivites()
    {
        return ActiviteDetente::with(['categorie', 'type'])->get();
    }
}