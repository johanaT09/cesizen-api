<?php

namespace Tests\Feature\Compte;

use App\Mail\ResetPasswordMail;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Cette méthode s'exécute automatiquement TOUT AU DÉBUT, avant chaque test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        DB::statement('PRAGMA foreign_keys = OFF;');
    }

    // TEST 1 : Demande de lien (Success)
    public function test_un_utilisateur_peut_demander_un_lien_de_reinitialisation()
    {
        Mail::fake();

        DB::table('utilisateur')->insert([
            'prenom' => 'Test',
            'email' => 'test-unitaire@cesizen.fr',
            'mot_de_passe' => Hash::make('Password123!'),
            'id_role' => 1,
            'id_genre' => 1,
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'test-unitaire@cesizen.fr',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        Mail::assertQueued(ResetPasswordMail::class, function ($mail) {
            return $mail->hasTo('test-unitaire@cesizen.fr');
        });
    }

    // TEST 2 : Demande de lien (Fail si l'email n'existe pas)
    public function test_la_demande_echoue_si_l_email_n_existe_pas()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'inconnu@cesi.fr',
        ]);

        $response->assertStatus(422);
    }

    // TEST 3 : Inscription (Success)
    public function test_un_visiteur_anonyme_peut_creer_un_compte()
    {
        DB::table('genre_utilisateur')->insert([
            'id_genre' => 1,
            'libelle_genre' => 'Non précisé',
        ]);

        DB::table('role')->insert([
            'id_role' => 1,
            'libelle' => 'Utilisateur',
        ]);

        $response = $this->postJson('/api/signup', [
            'prenom' => 'Johana',
            'nom' => 'Terrier',
            'email' => 'nouveau-compte@cesizen.fr',
            'mot_de_passe' => 'Password123!',
            'id_genre' => 1,
            'date_naissance' => '2000-01-01',
            'consentement_rgpd' => true,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['message', 'token', 'user']);
        $response->assertJsonPath('user.email', 'nouveau-compte@cesizen.fr');
        $response->assertJsonPath('user.prenom', 'Johana');

        $this->assertDatabaseHas('utilisateur', [
            'email' => 'nouveau-compte@cesizen.fr',
        ]);
    }

    // TEST 4 : Application du nouveau mot de passe (Success)
    public function test_un_utilisateur_peut_reinitialiser_son_mot_de_passe_avec_un_token_valide()
    {
        // 1. On crée l'utilisateur lié
        DB::table('utilisateur')->insert([
            'prenom' => 'Test',
            'email' => 'change-mdp@cesizen.fr',
            'mot_de_passe' => Hash::make('AncienMdp123!'),
            'id_role' => 1,
            'id_genre' => 1,
        ]);

        // 2. On simule un token valide stocké en base de données
        DB::table('password_reset_tokens')->insert([
            'email' => 'change-mdp@cesizen.fr',
            'token' => Hash::make('mon-super-token-secret'),
            'created_at' => now(),
        ]);

        // 3. On appelle ta route de réinitialisation finale
        $response = $this->postJson('/api/reset-password', [
            'token' => 'mon-super-token-secret',
            'email' => 'change-mdp@cesizen.fr',
            'password' => 'NouveauMdp123!',
            'password_confirmation' => 'NouveauMdp123!',
        ]);

        // 4. On vérifie que ton contrôleur répond positivement
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    // TEST 5 : Réinitialisation refusée si le token est invalide
    public function test_la_reinitialisation_echoue_avec_un_token_invalide()
    {
        DB::table('utilisateur')->insert([
            'prenom' => 'Test',
            'email' => 'token-invalide@cesizen.fr',
            'mot_de_passe' => Hash::make('AncienMdp123!'),
            'id_role' => 1,
            'id_genre' => 1,
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => 'token-invalide@cesizen.fr',
            'token' => Hash::make('le-bon-token'),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'token' => 'un-mauvais-token',
            'email' => 'token-invalide@cesizen.fr',
            'password' => 'NouveauMdp123!',
            'password_confirmation' => 'NouveauMdp123!',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => 'error']);
    }

    // TEST 6 : Réinitialisation refusée si le token a expiré
    public function test_la_reinitialisation_echoue_avec_un_token_expire()
    {
        DB::table('utilisateur')->insert([
            'prenom' => 'Test',
            'email' => 'token-expire@cesizen.fr',
            'mot_de_passe' => Hash::make('AncienMdp123!'),
            'id_role' => 1,
            'id_genre' => 1,
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => 'token-expire@cesizen.fr',
            'token' => Hash::make('token-perime'),
            'created_at' => now()->subHours(2),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'token' => 'token-perime',
            'email' => 'token-expire@cesizen.fr',
            'password' => 'NouveauMdp123!',
            'password_confirmation' => 'NouveauMdp123!',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => 'error']);
    }

    // TEST 7 : Connexion réussie
    public function test_un_utilisateur_peut_se_connecter_avec_les_bons_identifiants()
    {
        $utilisateur = Utilisateur::factory()->create([
            'email' => 'connexion@cesizen.fr',
            'mot_de_passe' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'connexion@cesizen.fr',
            'mot_de_passe' => 'Password123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'token', 'user' => ['id_utilisateur', 'prenom', 'email', 'id_role']]);
        $response->assertJsonPath('user.email', $utilisateur->email);
    }

    // TEST 8 : Connexion refusée avec un mauvais mot de passe
    public function test_la_connexion_echoue_avec_un_mauvais_mot_de_passe()
    {
        Utilisateur::factory()->create([
            'email' => 'mauvais-mdp@cesizen.fr',
            'mot_de_passe' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'mauvais-mdp@cesizen.fr',
            'mot_de_passe' => 'PasLeBonMdp!',
        ]);

        $response->assertStatus(401);
    }

    // TEST 9 : Connexion refusée avec un email inconnu
    public function test_la_connexion_echoue_avec_un_email_inconnu()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'personne@cesizen.fr',
            'mot_de_passe' => 'PeuImporte123!',
        ]);

        $response->assertStatus(401);
    }

    // TEST 10 : Un utilisateur connecté peut se déconnecter
    public function test_un_utilisateur_connecte_peut_se_deconnecter()
    {
        $utilisateur = Utilisateur::factory()->create();
        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout');

        $response->assertStatus(200);
    }

    // TEST 11 : La déconnexion nécessite d'être authentifié
    public function test_la_deconnexion_necessite_d_etre_authentifie()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }
}
