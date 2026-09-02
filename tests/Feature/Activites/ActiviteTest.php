<?php

namespace Tests\Feature\Activites;

use App\Models\ActiviteDetente;
use App\Models\CategorieActivite;
use App\Models\TypeActivite;
use App\Models\Utilisateur;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

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

    private function authHeader(Utilisateur $utilisateur): array
    {
        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return ['Authorization' => 'Bearer '.$token];
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
            'id_categorie' => 1,
            'est_actif' => true,
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
            'id_categorie' => 1,
            'est_actif' => true,
        ]);

        // 2. On appelle ta route de détail pour l'ID 99
        $response = $this->getJson('/api/activites/99');

        // 3. On vérifie que l'API renvoie bien l'activité
        $response->assertStatus(200);
        $response->assertJsonPath('data.titre_activite', 'Séance Yoga ASMR');
    }

    // TEST 2bis : Consulter une activité inconnue renvoie 404
    public function test_consulter_une_activite_inconnue_renvoie_404()
    {
        $response = $this->getJson('/api/activites/999999');

        $response->assertStatus(404);
    }

    // TEST 3 : Sécurité de l'ajout (Visiteur non connecté -> Doit être bloqué)
    public function test_un_visiteur_anonyme_ne_peut_pas_ajouter_une_activite()
    {
        $response = $this->postJson('/api/activite', [
            'titre_activite' => 'Activité piratée',
            'contenu_activite' => 'Tentative d\'intrusion',
            'id_type' => 1,
            'id_categorie' => 1,
        ]);

        $response->assertStatus(401);
    }

    // TEST 4 : Un utilisateur connecté (non admin) ne peut pas ajouter une activité
    public function test_un_utilisateur_non_admin_ne_peut_pas_ajouter_une_activite()
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->postJson('/api/activite', [
                'titre_activite' => 'Activité tentée',
                'duree_estimee' => '10',
                'id_type' => 1,
                'id_categorie' => 1,
            ]);

        $response->assertStatus(403);
    }

    // TEST 5 : Un utilisateur connecté peut ajouter/retirer une activité de ses favoris
    public function test_un_utilisateur_peut_ajouter_puis_retirer_une_activite_de_ses_favoris()
    {
        $utilisateur = Utilisateur::factory()->create();
        $activite = ActiviteDetente::factory()->create();

        $addResponse = $this->withHeaders($this->authHeader($utilisateur))
            ->postJson('/api/activites/'.$activite->id_activite.'/favori');

        $addResponse->assertStatus(200);
        $addResponse->assertJson(['is_favori' => true]);

        $this->assertDatabaseHas('favori', [
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'id_activite' => $activite->id_activite,
        ]);

        $removeResponse = $this->withHeaders($this->authHeader($utilisateur))
            ->postJson('/api/activites/'.$activite->id_activite.'/favori');

        $removeResponse->assertStatus(200);
        $removeResponse->assertJson(['is_favori' => false]);

        $this->assertDatabaseMissing('favori', [
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'id_activite' => $activite->id_activite,
        ]);
    }

    // TEST 6 : Un utilisateur peut lister ses favoris
    public function test_un_utilisateur_peut_lister_ses_favoris()
    {
        $utilisateur = Utilisateur::factory()->create();
        $activite = ActiviteDetente::factory()->create();
        $utilisateur->activitesFavoris()->attach($activite->id_activite);

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->getJson('/api/favoris');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    // TEST 7 : Gérer les favoris nécessite d'être authentifié
    public function test_gerer_les_favoris_necessite_d_etre_authentifie()
    {
        $activite = ActiviteDetente::factory()->create();

        $this->postJson('/api/activites/'.$activite->id_activite.'/favori')->assertStatus(401);
        $this->getJson('/api/favoris')->assertStatus(401);
    }

    // TEST 8 : Un utilisateur peut enregistrer sa progression sur une activité puis la consulter
    public function test_un_utilisateur_peut_enregistrer_et_consulter_sa_progression()
    {
        $utilisateur = Utilisateur::factory()->create();
        $activite = ActiviteDetente::factory()->create();

        $saveResponse = $this->withHeaders($this->authHeader($utilisateur))
            ->postJson('/api/activites/'.$activite->id_activite.'/session', [
                'progression' => 15,
                'est_termine' => false,
            ]);

        $saveResponse->assertStatus(200);

        $this->assertDatabaseHas('session_activite', [
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'id_activite' => $activite->id_activite,
            'duree_realisee' => 15,
        ]);

        $getResponse = $this->withHeaders($this->authHeader($utilisateur))
            ->getJson('/api/activites/'.$activite->id_activite.'/session');

        $getResponse->assertStatus(200);
        $getResponse->assertJson(['status' => 'success', 'progression' => 15]);
    }

    // TEST 9 : Un utilisateur peut consulter la liste de ses activités en cours
    public function test_un_utilisateur_peut_consulter_ses_activites_en_cours()
    {
        $utilisateur = Utilisateur::factory()->create();
        $activite = ActiviteDetente::factory()->create();

        DB::table('session_activite')->insert([
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'id_activite' => $activite->id_activite,
            'duree_realisee' => 5,
            'est_termine' => false,
            'date_session' => now()->toDateString(),
        ]);

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->getJson('/api/sessions/en-cours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    // TEST 10 : Un administrateur peut créer une activité
    public function test_un_administrateur_peut_creer_une_activite()
    {
        Storage::fake('public');

        $admin = Utilisateur::factory()->admin()->create();
        $type = TypeActivite::factory()->create();
        $categorie = CategorieActivite::factory()->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->post('/api/activite', [
                'titre_activite' => 'Nouvelle activité',
                'description_activite' => 'Une description',
                'duree_estimee' => '20',
                'id_type' => $type->id_type,
                'id_categorie' => $categorie->id_categorie,
                'image' => UploadedFile::fake()->create('cover.jpg', 10, 'image/jpeg'),
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('activite_detente', ['titre_activite' => 'Nouvelle activité']);
    }

    // TEST 11 : Un administrateur peut désactiver une activité
    public function test_un_administrateur_peut_desactiver_une_activite()
    {
        $admin = Utilisateur::factory()->admin()->create();
        $activite = ActiviteDetente::factory()->create(['est_actif' => true]);

        $response = $this->withHeaders($this->authHeader($admin))
            ->patchJson('/api/activite/'.$activite->id_activite.'/desactiver');

        $response->assertStatus(200);
        $this->assertDatabaseHas('activite_detente', [
            'id_activite' => $activite->id_activite,
            'est_actif' => false,
        ]);
    }

    // TEST 12 : Un non-administrateur ne peut pas accéder à la liste admin des activités
    public function test_un_non_administrateur_ne_peut_pas_consulter_les_activites_admin()
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->getJson('/api/admin/activites');

        $response->assertStatus(403);
    }

    // TEST 13 : Un administrateur peut consulter la liste admin des activités
    public function test_un_administrateur_peut_consulter_les_activites_admin()
    {
        $admin = Utilisateur::factory()->admin()->create();
        ActiviteDetente::factory()->count(2)->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->getJson('/api/admin/activites');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
    }
}
