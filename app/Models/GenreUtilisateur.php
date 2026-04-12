<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenreUtilisateur extends Model
{
    protected $table = 'genre_utilisateur';
    protected $primaryKey = 'id_genre';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'libelle_genre',
    ];

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'id_genre', 'id_genre');
    }
}
