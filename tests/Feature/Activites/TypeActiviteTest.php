<?php

namespace Tests\Feature\Activites;

use App\Models\TypeActivite;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TypeActiviteTest extends TestCase
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

    // TEST 1 : Un visiteur peut lister les types d'activités
    public function test_un_visiteur_peut_lister_les_types_d_activites()
    {
        TypeActivite::factory()->count(3)->create();

        $response = $this->getJson('/api/types-activites');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    // TEST 2 : Un visiteur peut consulter un type précis
    public function test_un_visiteur_peut_consulter_un_type_specifique()
    {
        $type = TypeActivite::factory()->create(['libelle_type' => 'Audio']);

        $response = $this->getJson('/api/types-activites/'.$type->id_type);

        $response->assertStatus(200);
        $response->assertJsonPath('libelle_type', 'Audio');
    }

    // TEST 3 : Consulter un type inconnu renvoie 404
    public function test_consulter_un_type_inconnu_renvoie_404()
    {
        $response = $this->getJson('/api/types-activites/999999');

        $response->assertStatus(404);
    }

    // TEST 4 : Un visiteur anonyme ne peut pas créer un type d'activité
    public function test_un_visiteur_anonyme_ne_peut_pas_creer_un_type()
    {
        $response = $this->postJson('/api/types-activites', [
            'libelle_type' => 'Nouveau type',
        ]);

        $response->assertStatus(401);
    }

    // TEST 5 : Un non-administrateur ne peut pas créer un type d'activité
    public function test_un_non_administrateur_ne_peut_pas_creer_un_type()
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->postJson('/api/types-activites', [
                'libelle_type' => 'Nouveau type',
            ]);

        $response->assertStatus(403);
    }

    // TEST 6 : Un administrateur peut créer un type d'activité
    public function test_un_administrateur_peut_creer_un_type()
    {
        $admin = Utilisateur::factory()->admin()->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->postJson('/api/types-activites', [
                'libelle_type' => 'Podcast',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('type', ['libelle_type' => 'Podcast']);
    }

    // TEST 7 : La création d'un type échoue avec des données invalides
    public function test_la_creation_d_un_type_echoue_sans_libelle()
    {
        $admin = Utilisateur::factory()->admin()->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->postJson('/api/types-activites', []);

        $response->assertStatus(422);
    }
}
