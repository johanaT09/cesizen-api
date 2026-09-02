<?php

namespace Tests\Unit\Models;

use App\Models\ActiviteDetente;
use App\Models\CategorieActivite;
use App\Models\GenreUtilisateur;
use App\Models\Information;
use App\Models\Role;
use App\Models\TypeActivite;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RelationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        DB::statement('PRAGMA foreign_keys = OFF;');
    }

    public function test_un_utilisateur_appartient_a_un_role()
    {
        $role = Role::factory()->create(['libelle' => 'Utilisateur']);
        $utilisateur = Utilisateur::factory()->create(['id_role' => $role->id_role]);

        $this->assertInstanceOf(Role::class, $utilisateur->role);
        $this->assertSame('Utilisateur', $utilisateur->role->libelle);
    }

    public function test_un_utilisateur_appartient_a_un_genre()
    {
        $genre = GenreUtilisateur::factory()->create(['libelle_genre' => 'Homme']);
        $utilisateur = Utilisateur::factory()->create(['id_genre' => $genre->id_genre]);

        $this->assertInstanceOf(GenreUtilisateur::class, $utilisateur->genre);
        $this->assertSame('Homme', $utilisateur->genre->libelle_genre);
    }

    public function test_une_activite_appartient_a_une_categorie()
    {
        $categorie = CategorieActivite::factory()->create(['libelle_categorie' => 'Relaxation']);
        $activite = ActiviteDetente::factory()->create(['id_categorie' => $categorie->id_categorie]);

        $this->assertInstanceOf(CategorieActivite::class, $activite->categorie);
        $this->assertSame('Relaxation', $activite->categorie->libelle_categorie);
    }

    public function test_une_activite_appartient_a_un_type()
    {
        $type = TypeActivite::factory()->create(['libelle_type' => 'Audio']);
        $activite = ActiviteDetente::factory()->create(['id_type' => $type->id_type]);

        $this->assertInstanceOf(TypeActivite::class, $activite->type);
        $this->assertSame('Audio', $activite->type->libelle_type);
    }

    public function test_un_utilisateur_peut_avoir_des_activites_favorites()
    {
        $utilisateur = Utilisateur::factory()->create();
        $activite = ActiviteDetente::factory()->create();

        $utilisateur->activitesFavoris()->attach($activite->id_activite);

        $this->assertCount(1, $utilisateur->activitesFavoris);
        $this->assertTrue($utilisateur->activitesFavoris->contains('id_activite', $activite->id_activite));
    }

    public function test_le_pivot_favori_peut_etre_detache()
    {
        $utilisateur = Utilisateur::factory()->create();
        $activite = ActiviteDetente::factory()->create();

        $utilisateur->activitesFavoris()->attach($activite->id_activite);
        $this->assertDatabaseHas('favori', [
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'id_activite' => $activite->id_activite,
        ]);

        $utilisateur->activitesFavoris()->detach($activite->id_activite);
        $this->assertDatabaseMissing('favori', [
            'id_utilisateur' => $utilisateur->id_utilisateur,
            'id_activite' => $activite->id_activite,
        ]);
    }

    public function test_une_activite_a_plusieurs_utilisateurs_qui_l_ont_mise_en_favori()
    {
        $activite = ActiviteDetente::factory()->create();
        $utilisateur1 = Utilisateur::factory()->create();
        $utilisateur2 = Utilisateur::factory()->create();

        $activite->utilisateursFavoris()->attach([$utilisateur1->id_utilisateur, $utilisateur2->id_utilisateur]);

        $this->assertCount(2, $activite->utilisateursFavoris);
    }

    public function test_une_information_appartient_a_un_utilisateur_et_une_categorie()
    {
        $categorie = CategorieActivite::factory()->create();
        $utilisateur = Utilisateur::factory()->create();
        $information = Information::factory()->create([
            'id_categorie' => $categorie->id_categorie,
            'id_utilisateur' => $utilisateur->id_utilisateur,
        ]);

        $this->assertInstanceOf(CategorieActivite::class, $information->categorie);
        $this->assertInstanceOf(Utilisateur::class, $information->utilisateur);
        $this->assertSame($utilisateur->id_utilisateur, $information->utilisateur->id_utilisateur);
    }
}
