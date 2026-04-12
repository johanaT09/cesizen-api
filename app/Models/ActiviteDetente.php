<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiviteDetente extends Model
{
    protected $table = 'activite_detente';
    protected $primaryKey = 'id_activite';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'titre_activite',
        'contenu_activite',
        'duree_estimee',
        'est_actif',
        'id_type',
        'id_categorie',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'est_actif' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(TypeActivite::class, 'id_type', 'id_type');
    }

    public function categorie()
    {
        return $this->belongsTo(CategorieActivite::class, 'id_categorie', 'id_categorie');
    }

    public function sessions()
    {
        return $this->hasMany(SessionActivite::class, 'id_activite', 'id_activite');
    }

    public function utilisateursFavoris()
    {
        return $this->belongsToMany(Utilisateur::class, 'favori', 'id_activite', 'id_utilisateur');
    }
}
