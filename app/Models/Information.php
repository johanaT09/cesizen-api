<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $table = 'information';
    protected $primaryKey = 'id_information';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'titre_information',
        'contenu_information',
        'date_publication_information',
        'est_actif',
        'id_categorie',
        'id_utilisateur',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'date_publication_information' => 'date',
        'est_actif' => 'boolean',
    ];

    public function categorie()
    {
        return $this->belongsTo(CategorieActivite::class, 'id_categorie', 'id_categorie');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }
}
