<?php

namespace Database\Factories;

use App\Models\ActiviteDetente;
use App\Models\SessionActivite;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SessionActivite>
 */
class SessionActiviteFactory extends Factory
{
    protected $model = SessionActivite::class;

    public function definition(): array
    {
        return [
            'date_session' => fake()->date(),
            'duree_realisee' => (string) fake()->numberBetween(1, 30),
            'id_activite' => ActiviteDetente::factory(),
            'id_utilisateur' => Utilisateur::factory(),
            'est_termine' => false,
        ];
    }
}
