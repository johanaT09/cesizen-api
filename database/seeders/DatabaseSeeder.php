<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use App\Models\TypeActivite;
use App\Models\CategorieActivite;
use App\Models\ActiviteDetente;
use App\Models\Favori;
use App\Models\GenreUtilisateur;
use App\Models\Information;
use App\Models\Role;
use App\Models\SessionActivite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création des rôles
        $roleUser = Role::create(['libelle' => 'Utilisateur']);
        $roleAdmin = Role::create(['libelle' => 'Admin']);

        // Création des genres
        $genreHomme = GenreUtilisateur::create(['libelle_genre' => 'Homme']);
        $genreFemme = GenreUtilisateur::create(['libelle_genre' => 'Femme']);

        // Création de 20 utilisateurs "utilisateur"
        $utilisateurs = collect();
        for ($i = 0; $i < 20; $i++) {
            $utilisateurs->push(Utilisateur::create([
                'prenom' => 'User' . $i,
                'date_naissance' => now()->subYears(rand(18, 50))->format('Y-m-d'),
                'email' => 'user' . $i . '@example.com',
                'mot_de_passe' => Hash::make('password'),
                'consentement_rgpd' => now(),
                'est_actif' => true,
                'date_anonymisation' => null,
                'id_genre' => rand(0, 1) ? $genreHomme->id_genre : $genreFemme->id_genre,
                'id_role' => $roleUser->id_role,
            ]));
        }

        // Création d'un admin
        $admin = Utilisateur::create([
            'prenom' => 'Johana',
            'date_naissance' => '2004-11-16',
            'email' => 'johana.terrier@viacesi.fr',
            'mot_de_passe' => Hash::make('mot_de_passe'),
            'consentement_rgpd' => now(),
            'est_actif' => true,
            'date_anonymisation' => null,
            'id_genre' => $genreFemme->id_genre,
            'id_role' => $roleAdmin->id_role,
        ]);

        // Création de types d'activités
        $typeSport = TypeActivite::create(['libelle_type' => 'Détente']);
        $typeCulture = TypeActivite::create(['libelle_type' => 'Nutrition']);
        $typeCulture = TypeActivite::create(['libelle_type' => 'Bien-être']);

        // Création de catégories d'activités
        $catBienEtre = CategorieActivite::create(['libelle_categorie' => 'Bien-être']);
        $catLoisir = CategorieActivite::create(['libelle_categorie' => 'Loisir']);

        // Création d'activités de détente
        $activites = collect();
        for ($i = 0; $i < 10; $i++) {
            $activites->push(ActiviteDetente::create([
                'titre_activite' => 'Activité ' . $i,
                'contenu_activite' => 'Description activité ' . $i,
                'duree_estimee' => rand(30, 120),
                'est_actif' => true,
                'id_type' => rand(0, 1) ? $typeSport->id_type : $typeCulture->id_type,
                'id_categorie' => rand(0, 1) ? $catBienEtre->id_categorie : $catLoisir->id_categorie,
            ]));
        }

        // Création de favoris
        foreach ($utilisateurs as $utilisateur) {
            Favori::create([
                'id_utilisateur' => $utilisateur->id_utilisateur,
                'id_activite' => $activites->random()->id_activite,
            ]);
        }

        // Création d'informations
        for ($i = 0; $i < 5; $i++) {
            Information::create([
                'titre_information' => 'Info ' . $i,
                'contenu_information' => 'Contenu info ' . $i,
                'date_publication_information' => now()->subDays(rand(1, 100)),
                'est_actif' => true,
                'id_categorie' => $catBienEtre->id_categorie,
                'id_utilisateur' => $utilisateurs->random()->id_utilisateur,
            ]);
        }

        // Création de sessions d'activités
        foreach ($utilisateurs as $utilisateur) {
            SessionActivite::create([
                'date_session' => now()->subDays(rand(1, 30)),
                'duree_realisee' => rand(30, 120),
                'id_activite' => $activites->random()->id_activite,
                'id_utilisateur' => $utilisateur->id_utilisateur,
            ]);
        }
    }
}
