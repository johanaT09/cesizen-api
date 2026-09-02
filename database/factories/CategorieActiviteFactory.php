<?php

namespace Database\Factories;

use App\Models\CategorieActivite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategorieActivite>
 */
class CategorieActiviteFactory extends Factory
{
    protected $model = CategorieActivite::class;

    public function definition(): array
    {
        return [
            'libelle_categorie' => fake()->unique()->word(),
        ];
    }
}
