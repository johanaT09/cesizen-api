<?php

namespace Tests\Feature\Informations;

use App\Models\CategorieActivite;
use App\Models\Information;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategorieActiviteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        DB::statement('PRAGMA foreign_keys = OFF;');
    }

    private function authHeader(Utilisateur $utilisateur): array
    {
        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return ['Authorization' => 'Bearer '.$token];
    }

    // TEST 1 : Un visiteur peut lister les catégories
    public function test_un_visiteur_peut_lister_les_categories()
    {
        CategorieActivite::factory()->count(2)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    // TEST 2 : Un visiteur anonyme ne peut pas créer de catégorie
    public function test_un_visiteur_anonyme_ne_peut_pas_creer_une_categorie()
    {
        $response = $this->postJson('/api/addcategorie', [
            'libelle_categorie' => 'Nouvelle catégorie',
        ]);

        $response->assertStatus(401);
    }

    // TEST 3 : Un non-administrateur ne peut pas créer de catégorie
    public function test_un_non_administrateur_ne_peut_pas_creer_une_categorie()
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->postJson('/api/addcategorie', [
                'libelle_categorie' => 'Nouvelle catégorie',
            ]);

        $response->assertStatus(403);
    }

    // TEST 4 : Un administrateur peut créer une catégorie
    public function test_un_administrateur_peut_creer_une_categorie()
    {
        $admin = Utilisateur::factory()->admin()->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->postJson('/api/addcategorie', [
                'libelle_categorie' => 'Bien-être',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categorie_activite', ['libelle_categorie' => 'Bien-être']);
    }

    // TEST 5 : La création échoue si le libellé existe déjà
    public function test_la_creation_d_une_categorie_echoue_si_le_libelle_existe_deja()
    {
        $admin = Utilisateur::factory()->admin()->create();
        CategorieActivite::factory()->create(['libelle_categorie' => 'Sommeil']);

        $response = $this->withHeaders($this->authHeader($admin))
            ->postJson('/api/addcategorie', [
                'libelle_categorie' => 'Sommeil',
            ]);

        $response->assertStatus(422);
    }

    // TEST 6 : Un administrateur peut mettre à jour une catégorie
    public function test_un_administrateur_peut_mettre_a_jour_une_categorie()
    {
        $admin = Utilisateur::factory()->admin()->create();
        $categorie = CategorieActivite::factory()->create(['libelle_categorie' => 'Ancien nom']);

        $response = $this->withHeaders($this->authHeader($admin))
            ->putJson('/api/categories/'.$categorie->id_categorie, [
                'libelle_categorie' => 'Nouveau nom',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categorie_activite', [
            'id_categorie' => $categorie->id_categorie,
            'libelle_categorie' => 'Nouveau nom',
        ]);
    }

    // TEST 7 : Un administrateur peut supprimer une catégorie sans relations
    public function test_un_administrateur_peut_supprimer_une_categorie_sans_relations()
    {
        $admin = Utilisateur::factory()->admin()->create();
        $categorie = CategorieActivite::factory()->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->deleteJson('/api/categories/'.$categorie->id_categorie);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categorie_activite', ['id_categorie' => $categorie->id_categorie]);
    }

    // TEST 8 : Suppression refusée si la catégorie a des informations liées
    public function test_la_suppression_est_refusee_si_la_categorie_a_des_informations_liees()
    {
        $admin = Utilisateur::factory()->admin()->create();
        $categorie = CategorieActivite::factory()->create();
        Information::factory()->create(['id_categorie' => $categorie->id_categorie]);

        $response = $this->withHeaders($this->authHeader($admin))
            ->deleteJson('/api/categories/'.$categorie->id_categorie);

        $response->assertStatus(422);
        $this->assertDatabaseHas('categorie_activite', ['id_categorie' => $categorie->id_categorie]);
    }
}
