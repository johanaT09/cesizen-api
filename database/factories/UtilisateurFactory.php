<?php

namespace Database\Factories;

use App\Models\GenreUtilisateur;
use App\Models\Role;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    public function definition(): array
    {
        return [
            'prenom' => fake()->firstName(),
            'date_naissance' => fake()->date(),
            'email' => fake()->unique()->safeEmail(),
            'mot_de_passe' => Hash::make('Password123!'),
            'consentement_rgpd' => now(),
            'est_actif' => true,
            'date_anonymisation' => null,
            'id_genre' => GenreUtilisateur::factory(),
            'id_role' => Role::factory(),
        ];
    }

    /**
     * The IsAdmin middleware hard-codes id_role === 2 as the administrator role,
     * so the admin state must guarantee a role with that exact id exists.
     */
    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            DB::table('role')->updateOrInsert(
                ['id_role' => 2],
                ['libelle' => 'Administrateur']
            );

            return [
                'id_role' => 2,
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'est_actif' => false,
        ]);
    }
}
