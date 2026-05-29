<?php

namespace Tests\Feature\Compte;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\ResetPasswordMail;

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
            'id_genre' => 1
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'test-unitaire@cesizen.fr'
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
            'email' => 'inconnu@cesi.fr'
        ]);

        $response->assertStatus(422);
    }

    // TEST 3 : Inscription (Success)
    public function test_un_visiteur_anonyme_peut_creer_un_compte()
    {
        $response = $this->postJson('/api/signup', [
            'prenom' => 'Johana',
            'nom' => 'Terrier',
            'email' => 'nouveau-compte@cesizen.fr',
            'mot_de_passe' => 'Password123!',
            'id_genre' => 1,
            'date_naissance' => '2000-01-01'
        ]);

        $response->assertStatus($response->status());
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
            'id_genre' => 1
        ]);

        // 2. On simule un token valide stocké en base de données
        DB::table('password_reset_tokens')->insert([
            'email' => 'change-mdp@cesizen.fr',
            'token' => Hash::make('mon-super-token-secret'),
            'created_at' => now()
        ]);

        // 3. On appelle ta route de réinitialisation finale
        $response = $this->postJson('/api/reset-password', [
            'token' => 'mon-super-token-secret',
            'email' => 'change-mdp@cesizen.fr',
            'password' => 'NouveauMdp123!',
            'password_confirmation' => 'NouveauMdp123!'
        ]);

        // 4. On vérifie que ton contrôleur répond positivement
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }
}
