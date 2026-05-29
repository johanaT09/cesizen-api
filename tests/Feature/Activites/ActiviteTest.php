<?php

namespace Tests\Feature\Activites;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ActiviteTest extends TestCase
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

    // TEST 1 : Consulter le catalogue (Visiteur)
    public function test_un_visiteur_peut_consulter_le_catalogue_des_activites()
    {
        // 1. On insère une fausse activité avec les vrais noms de la BDD
        DB::table('activite_detente')->insert([
            'id_activite' => 1,
            'titre_activite' => 'Méditation Guidée',
            'contenu_activite' => '10 minutes pour relâcher la pression.',
            'id_type' => 1,
            'id_categorie' => 1
        ]);

        // 2. On appelle ta route GET pour lister les activités
        $response = $this->getJson('/api/activites');

        // 3. On vérifie que ça répond 200 OK
        $response->assertStatus(200);
    }

    // TEST 2 : Consulter une activité spécifique (Visiteur)
    public function test_un_visiteur_peut_consulter_une_activite_specifique_par_son_id()
    {
        // 1. On insère une activité avec un ID connu
        DB::table('activite_detente')->insert([
            'id_activite' => 99,
            'titre_activite' => 'Séance Yoga ASMR',
            'contenu_activite' => 'Idéal pour décompresser avant de dormir.',
            'id_type' => 1,
            'id_categorie' => 1
        ]);

        // 2. On appelle ta route de détail pour l'ID 99
        $response = $this->getJson('/api/activites/99');

        // 3. On vérifie que l'API renvoie bien l'activité
        $response->assertStatus(200);
    }

    // TEST 3 : Sécurité de l'ajout (Visiteur non connecté -> Doit être bloqué)
    public function test_un_visiteur_anonyme_ne_peut_pas_ajouter_une_activite()
    {
        $response = $this->postJson('/api/activite', [
            'titre_activite' => 'Activité piratée',
            'contenu_activite' => 'Tentative d\'intrusion',
            'id_type' => 1,
            'id_categorie' => 1
        ]);

        $response->assertStatus(401);
    }
}