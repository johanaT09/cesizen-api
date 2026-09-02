<?php

namespace Database\Factories;

use App\Models\ActiviteDetente;
use App\Models\CategorieActivite;
use App\Models\TypeActivite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActiviteDetente>
 */
class ActiviteDetenteFactory extends Factory
{
    protected $model = ActiviteDetente::class;

    public function definition(): array
    {
        return [
            'titre_activite' => fake()->sentence(3),
            'contenu_activite' => fake()->paragraph(),
            'duree_estimee' => fake()->numberBetween(5, 60),
            'est_actif' => true,
            'image_path' => null,
            'id_type' => TypeActivite::factory(),
            'id_categorie' => CategorieActivite::factory(),
            'lien_ressource' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }
}
