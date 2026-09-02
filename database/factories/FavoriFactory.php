<?php

namespace Database\Factories;

use App\Models\ActiviteDetente;
use App\Models\Favori;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Favori>
 */
class FavoriFactory extends Factory
{
    protected $model = Favori::class;

    public function definition(): array
    {
        return [
            'id_utilisateur' => Utilisateur::factory(),
            'id_activite' => ActiviteDetente::factory(),
        ];
    }
}
