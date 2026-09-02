<?php

namespace Database\Factories;

use App\Models\TypeActivite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TypeActivite>
 */
class TypeActiviteFactory extends Factory
{
    protected $model = TypeActivite::class;

    public function definition(): array
    {
        return [
            'libelle_type' => fake()->unique()->randomElement(['Audio', 'Vidéo', 'Article', 'Exercice']),
        ];
    }
}
