<?php

namespace Tests\Feature\Compte;

use App\Models\GenreUtilisateur;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UtilisateurTest extends TestCase
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

    // TEST 1 : Un utilisateur connecté peut consulter son profil
    public function test_un_utilisateur_connecte_peut_consulter_son_profil()
    {
        $genre = GenreUtilisateur::factory()->create(['libelle_genre' => 'Femme']);
        $utilisateur = Utilisateur::factory()->create([
            'prenom' => 'Alice',
            'email' => 'alice@cesizen.fr',
            'id_genre' => $genre->id_genre,
        ]);

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->getJson('/api/mon-profil');

        $response->assertStatus(200);
        $response->assertJson([
            'prenom' => 'Alice',
            'email' => 'alice@cesizen.fr',
            'libelle_genre' => 'Femme',
        ]);
    }

    // TEST 2 : Consulter son profil nécessite d'être authentifié
    public function test_consulter_son_profil_necessite_d_etre_authentifie()
    {
        $response = $this->getJson('/api/mon-profil');

        $response->assertStatus(401);
    }

    // TEST 3 : Un utilisateur peut mettre à jour son profil
    public function test_un_utilisateur_peut_mettre_a_jour_son_profil()
    {
        $utilisateur = Utilisateur::factory()->create(['prenom' => 'Ancien']);

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->putJson('/api/utilisateur', [
                'prenom' => 'Nouveau',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('utilisateur.prenom', 'Nouveau');

        $this->assertDatabaseHas('utilisateur', [
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'prenom' => 'Nouveau',
        ]);
    }

    // TEST 4 : La mise à jour du profil échoue si le mot de passe actuel est incorrect
    public function test_la_mise_a_jour_du_mot_de_passe_echoue_si_le_mot_de_passe_actuel_est_incorrect()
    {
        $utilisateur = Utilisateur::factory()->create([
            'mot_de_passe' => Hash::make('BonMotDePasse123!'),
        ]);

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->putJson('/api/utilisateur', [
                'current_password' => 'MauvaisMotDePasse!',
                'new_password' => 'NouveauMotDePasse123!',
                'new_password_confirmation' => 'NouveauMotDePasse123!',
            ]);

        $response->assertStatus(422);
    }

    // TEST 5 : La mise à jour du profil échoue si les données sont invalides
    public function test_la_mise_a_jour_du_profil_echoue_avec_des_donnees_invalides()
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->putJson('/api/utilisateur', [
                'date_naissance' => 'pas-une-date',
            ]);

        $response->assertStatus(422);
    }

    // TEST 6 : Mettre à jour son profil nécessite d'être authentifié
    public function test_la_mise_a_jour_du_profil_necessite_d_etre_authentifie()
    {
        $response = $this->putJson('/api/utilisateur', ['prenom' => 'X']);

        $response->assertStatus(401);
    }

    // TEST 7 : Un utilisateur peut supprimer (anonymiser) son propre compte
    public function test_un_utilisateur_peut_supprimer_son_compte()
    {
        $utilisateur = Utilisateur::factory()->create(['email' => 'a-supprimer@cesizen.fr']);

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->deleteJson('/api/supprimer-compte');

        $response->assertStatus(200);

        $utilisateur->refresh();
        $this->assertNotEquals('a-supprimer@cesizen.fr', $utilisateur->email);
        $this->assertFalse((bool) $utilisateur->est_actif);
    }

    // TEST 8 : La suppression du compte nécessite d'être authentifié
    public function test_la_suppression_du_compte_necessite_d_etre_authentifie()
    {
        $response = $this->deleteJson('/api/supprimer-compte');

        $response->assertStatus(401);
    }

    // TEST 9 : Un administrateur peut lister les utilisateurs
    public function test_un_administrateur_peut_lister_les_utilisateurs()
    {
        $admin = Utilisateur::factory()->admin()->create();
        Utilisateur::factory()->count(3)->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->getJson('/api/utilisateurs');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
    }

    // TEST 10 : Un non-administrateur ne peut pas lister les utilisateurs
    public function test_un_non_administrateur_ne_peut_pas_lister_les_utilisateurs()
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->withHeaders($this->authHeader($utilisateur))
            ->getJson('/api/utilisateurs');

        $response->assertStatus(403);
    }

    // TEST 11 : Un administrateur peut créer un compte utilisateur
    public function test_un_administrateur_peut_creer_un_utilisateur()
    {
        $admin = Utilisateur::factory()->admin()->create();
        $genre = GenreUtilisateur::factory()->create();
        $role = \App\Models\Role::factory()->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->postJson('/api/utilisateur/admin-create', [
                'prenom' => 'Cree',
                'email' => 'cree-par-admin@cesizen.fr',
                'mot_de_passe' => 'Password123!',
                'date_naissance' => '1990-01-01',
                'id_genre' => $genre->id_genre,
                'id_role' => $role->id_role,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('utilisateur', ['email' => 'cree-par-admin@cesizen.fr']);
    }

    // TEST 12 : Un administrateur peut désactiver un utilisateur
    public function test_un_administrateur_peut_desactiver_un_utilisateur()
    {
        $admin = Utilisateur::factory()->admin()->create();
        $utilisateur = Utilisateur::factory()->create(['est_actif' => true]);

        $response = $this->withHeaders($this->authHeader($admin))
            ->patchJson('/api/utilisateur/'.$utilisateur->id_utilisateur.'/desactiver');

        $response->assertStatus(200);
        $this->assertDatabaseHas('utilisateur', [
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'est_actif' => false,
        ]);
    }

    // TEST 13 : Un administrateur peut consulter la liste des rôles
    public function test_un_administrateur_peut_consulter_les_roles()
    {
        $admin = Utilisateur::factory()->admin()->create();
        \App\Models\Role::factory()->count(2)->create();

        $response = $this->withHeaders($this->authHeader($admin))
            ->getJson('/api/roles');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
    }
}
