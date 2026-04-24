<?php

namespace App\Repositories\ActiviteDetente;

use App\Models\ActiviteDetente;

class ActiviteRepository
{
    public function getAllActivites()
    {
        return ActiviteDetente::with(['categorie', 'type'])->get();
    }

    public function getActiviteById($id)
    {
        return ActiviteDetente::with(['categorie', 'type'])->find($id);
    }

    public function getActivitesByType($typeId)
    {
        return ActiviteDetente::with(['categorie', 'type'])
            ->where('id_type', $typeId)
            ->get();
    }
}
