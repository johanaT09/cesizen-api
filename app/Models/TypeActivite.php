<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeActivite extends Model
{
    protected $table = 'type';
    protected $primaryKey = 'id_type';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'libelle_type',
    ];

    public function activites()
    {
        return $this->hasMany(ActiviteDetente::class, 'id_type', 'id_type');
    }
}
