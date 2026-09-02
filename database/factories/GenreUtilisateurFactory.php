<?php

namespace Database\Factories;

use App\Models\GenreUtilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GenreUtilisateur>
 */
class GenreUtilisateurFactory extends Factory
{
    protected $model = GenreUtilisateur::class;

    public function definition(): array
    {
        return [
            'libelle_genre' => fake()->unique()->randomElement(['Femme', 'Homme', 'Autre', 'Non précisé']),
        ];
    }
}
