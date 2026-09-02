<?php

namespace Tests\Feature\Informations;

use App\Models\CategorieActivite;
use App\Models\Information;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

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
            'id_utilisateur' => 1, // 💻 LE FIX : On associe un auteur fictif
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
            'id_utilisateur' => 1, // 💻 LE FIX : On associe un auteur fictif
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
            'id_categorie' => 1,
        ]);

        $response->assertStatus(401);
    }

    private function authHeader(Utilisateur $utilisateur): array
    {
        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return ['Authorization' => 'Bearer '.$token];
    }

    // TEST 4 : Un non-administrateur ne peut pas créer une information
    public function test_un_non_administrateur_ne_peut_pas_creer_une_information()
    {
        $utilisateur = Utilisateur::factory()->create();
        $categorie = CategorieActivite::factory()->create();

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->postJson('/api/information', [
                'titre_information' => 'Article tenté',
                'contenu_information' => 'Contenu',
                'id_categorie' => $categorie->id_categorie,
            ]);

        $response->assertStatus(403);
    }

    // TEST 5 : Un administrateur peut créer une information
    public function test_un_administrateur_peut_creer_une_information()
    {
        $admin = Utilisateur::factory()->admin()->create();
        $categorie = CategorieActivite::factory()->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->postJson('/api/information', [
                'titre_information' => 'Nouvelle information',
                'contenu_information' => 'Contenu de l\'information',
                'id_categorie' => $categorie->id_categorie,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('information', ['titre_information' => 'Nouvelle information']);
    }

    // TEST 6 : Un administrateur peut mettre à jour une information
    public function test_un_administrateur_peut_mettre_a_jour_une_information()
    {
        $admin = Utilisateur::factory()->admin()->create();
        $categorie = CategorieActivite::factory()->create();
        $information = Information::factory()->create(['titre_information' => 'Ancien titre']);

        $response = $this->withHeaders($this->authHeader($admin))
            ->putJson('/api/information/'.$information->id_information, [
                'titre_information' => 'Nouveau titre',
                'contenu_information' => 'Nouveau contenu',
                'id_categorie' => $categorie->id_categorie,
                'est_actif' => true,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('information', [
            'id_information' => $information->id_information,
            'titre_information' => 'Nouveau titre',
        ]);
    }

    // TEST 7 : Un administrateur peut basculer le statut d'une information
    public function test_un_administrateur_peut_basculer_le_statut_d_une_information()
    {
        $admin = Utilisateur::factory()->admin()->create();
        $information = Information::factory()->create(['est_actif' => true]);

        $response = $this->withHeaders($this->authHeader($admin))
            ->patchJson('/api/information/'.$information->id_information.'/statut');

        $response->assertStatus(200);
        $this->assertDatabaseHas('information', [
            'id_information' => $information->id_information,
            'est_actif' => false,
        ]);
    }

    // TEST 8 : Un non-administrateur ne peut pas accéder à la liste admin des informations
    public function test_un_non_administrateur_ne_peut_pas_consulter_les_informations_admin()
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->getJson('/api/admin/informations');

        $response->assertStatus(403);
    }

    // TEST 9 : Un administrateur peut consulter la liste admin des informations
    public function test_un_administrateur_peut_consulter_les_informations_admin()
    {
        $admin = Utilisateur::factory()->admin()->create();
        Information::factory()->count(2)->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->getJson('/api/admin/informations');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
    }

    // TEST 10 : La création d'une information échoue avec des données invalides
    public function test_la_creation_d_une_information_echoue_avec_des_donnees_invalides()
    {
        $admin = Utilisateur::factory()->admin()->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->postJson('/api/information', [
                'titre_information' => '',
            ]);

        $response->assertStatus(422);
    }
}
