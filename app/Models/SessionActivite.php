<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionActivite extends Model
{
    protected $table = 'session_activite';
    protected $primaryKey = 'id_session';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'date_session',
        'duree_realisee',
        'id_activite',
        'id_utilisateur',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'date_session' => 'date',
    ];

    public function activite()
    {
        return $this->belongsTo(ActiviteDetente::class, 'id_activite', 'id_activite');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }
}
