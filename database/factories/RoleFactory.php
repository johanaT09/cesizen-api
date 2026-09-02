<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'libelle' => fake()->unique()->word().'-'.fake()->unique()->numberBetween(1, 1000000),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'libelle' => 'Administrateur',
        ]);
    }
}
