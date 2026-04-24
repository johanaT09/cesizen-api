<?php

namespace App\Repositories\ActiviteDetente;

use App\Models\TypeActivite;

class TypeActiviteRepository
{
    public function getAllTypes()
    {
        return TypeActivite::all();
    }

    public function getTypeById($id)
    {
        return TypeActivite::find($id);
    }

    public function createType(array $data)
    {
        return TypeActivite::create($data);
    }
}
