<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieActivite extends Model
{
    protected $table = 'categorie_activite';
    protected $primaryKey = 'id_categorie';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'libelle_categorie',
    ];

    public function activites()
    {
        return $this->hasMany(ActiviteDetente::class, 'id_categorie', 'id_categorie');
    }

    public function informations()
    {
        return $this->hasMany(Information::class, 'id_categorie', 'id_categorie');
    }
}
