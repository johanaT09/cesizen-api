<?php

namespace Database\Factories;

use App\Models\CategorieActivite;
use App\Models\Information;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Information>
 */
class InformationFactory extends Factory
{
    protected $model = Information::class;

    public function definition(): array
    {
        return [
            'titre_information' => fake()->sentence(4),
            'contenu_information' => fake()->paragraph(),
            'date_publication_information' => fake()->date(),
            'est_actif' => true,
            'id_categorie' => CategorieActivite::factory(),
            'id_utilisateur' => Utilisateur::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }
}
