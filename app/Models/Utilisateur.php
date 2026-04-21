<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends \Illuminate\Foundation\Auth\User
{
    use HasApiTokens;

    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'prenom',
        'date_naissance',
        'email',
        'mot_de_passe',
        'consentement_rgpd',
        'est_actif',
        'date_anonymisation',
        'id_genre',
        'id_role',
    ];

    /**
     * Hidden attributes for arrays/JSON
     */
    protected $hidden = [
        'mot_de_passe',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'date_naissance' => 'date',
        'consentement_rgpd' => 'datetime',
        'date_anonymisation' => 'datetime',
        'est_actif' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function genre()
    {
        return $this->belongsTo(GenreUtilisateur::class, 'id_genre', 'id_genre');
    }

    public function informations()
    {
        return $this->hasMany(Information::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function sessions()
    {
        return $this->hasMany(SessionActivite::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function activitesFavoris()
    {
        return $this->belongsToMany(ActiviteDetente::class, 'favori', 'id_utilisateur', 'id_activite');
    }
}
