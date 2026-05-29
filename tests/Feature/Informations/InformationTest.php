<?php

namespace Tests\Feature\Informations;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class InformationTest extends TestCase
{
    /**
     * Configuration initiale avant chaque test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        DB::statement('PRAGMA foreign_keys = OFF;');
    }

    // TEST 1 : Récupérer toute la liste (Visiteur)
    public function test_un_visiteur_peut_recuperer_la_liste_toutes_les_informations()
    {
        // 1. On simule la présence d'un article complet
        DB::table('information')->insert([
            'id_information' => 1,
            'titre_information' => 'Conseil Sommeil',
            'contenu_information' => 'Pour mieux dormir, évitez les écrans.',
            'id_categorie' => 1,
            'id_utilisateur' => 1 // 💻 LE FIX : On associe un auteur fictif
        ]);

        // 2. On appelle ta route de liste
        $response = $this->getJson('/api/informations');

        // 3. On vérifie que ça répond 200 OK
        $response->assertStatus(200);
    }

    // TEST 2 : Récupérer une information précise par son ID (Visiteur)
    public function test_un_visiteur_peut_recuperer_une_information_specifique_par_son_id()
    {
        // 1. On insère un article complet
        DB::table('information')->insert([
            'id_information' => 42,
            'titre_information' => 'Gestion du Stress',
            'contenu_information' => 'Respirez profondément pendant 5 minutes.',
            'id_categorie' => 1,
            'id_utilisateur' => 1 // 💻 LE FIX : On associe un auteur fictif
        ]);

        // 2. On appelle ta route de détail avec l'ID 42
        $response = $this->getJson('/api/information/42');

        // 3. On vérifie que l'API le trouve bien
        $response->assertStatus(200);
    }

    // TEST 3 : Sécurité de la création (Visiteur non connecté -> Doit être bloqué)
    public function test_un_visiteur_anonyme_ne_peut_pas_creer_une_information()
    {
        $response = $this->postJson('/api/information', [
            'titre_information' => 'Article piraté',
            'contenu_information' => 'Tentative d\'injection',
            'id_categorie' => 1
        ]);

        $response->assertStatus(401);
    }
}
