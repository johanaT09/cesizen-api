<?php

namespace App\Repositories\ActiviteDetente;

use App\Models\TypeActivite;

class TypeActiviteRepository
{
    public function getAllTypes()
    {
        return TypeActivite::all();
    }
}
