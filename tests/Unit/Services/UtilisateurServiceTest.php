<?php

namespace Tests\Unit\Services;

use App\Repositories\Compte\UtilisateurRepository;
use App\Services\Compte\UtilisateurService;
use Mockery;
use Tests\TestCase;

class UtilisateurServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_utilisateur_by_id_delegue_au_repository()
    {
        $repository = Mockery::mock(UtilisateurRepository::class);
        $repository->shouldReceive('findById')->once()->with(5)->andReturn('un-utilisateur');

        $service = new UtilisateurService($repository);

        $this->assertSame('un-utilisateur', $service->getUtilisateurById(5));
    }

    public function test_update_utilisateur_retourne_null_si_l_utilisateur_n_existe_pas()
    {
        $repository = Mockery::mock(UtilisateurRepository::class);
        $repository->shouldReceive('findById')->once()->with(99)->andReturn(null);
        $repository->shouldNotReceive('updateUtilisateur');

        $service = new UtilisateurService($repository);

        $this->assertNull($service->updateUtilisateur(99, ['prenom' => 'X']));
    }

    public function test_update_utilisateur_leve_une_exception_si_le_mot_de_passe_actuel_est_incorrect()
    {
        $user = (object) ['mot_de_passe' => password_hash('BonMotDePasse', PASSWORD_BCRYPT)];

        $repository = Mockery::mock(UtilisateurRepository::class);
        $repository->shouldReceive('findById')->once()->with(1)->andReturn($user);
        $repository->shouldNotReceive('updateUtilisateur');

        $service = new UtilisateurService($repository);

        $this->expectException(\InvalidArgumentException::class);

        $service->updateUtilisateur(1, [
            'current_password' => 'MauvaisMotDePasse',
            'new_password' => 'NouveauMotDePasse123',
        ]);
    }

    public function test_update_utilisateur_met_a_jour_le_mot_de_passe_si_l_ancien_est_correct()
    {
        $user = (object) ['mot_de_passe' => password_hash('BonMotDePasse', PASSWORD_BCRYPT)];

        $repository = Mockery::mock(UtilisateurRepository::class);
        $repository->shouldReceive('findById')->once()->with(1)->andReturn($user);
        $repository->shouldReceive('updateUtilisateur')
            ->once()
            ->with(1, Mockery::on(function ($data) {
                return isset($data['mot_de_passe'])
                    && ! isset($data['current_password'])
                    && ! isset($data['new_password'])
                    && ! isset($data['new_password_confirmation']);
            }))
            ->andReturn('utilisateur-maj');

        $service = new UtilisateurService($repository);

        $result = $service->updateUtilisateur(1, [
            'current_password' => 'BonMotDePasse',
            'new_password' => 'NouveauMotDePasse123',
            'new_password_confirmation' => 'NouveauMotDePasse123',
        ]);

        $this->assertSame('utilisateur-maj', $result);
    }

    public function test_desactiver_utilisateur_by_admin_delegue_au_repository()
    {
        $repository = Mockery::mock(UtilisateurRepository::class);
        $repository->shouldReceive('updateUtilisateurByAdmin')
            ->once()
            ->with(3, ['est_actif' => false])
            ->andReturn('desactive');

        $service = new UtilisateurService($repository);

        $this->assertSame('desactive', $service->desactiverUtilisateurByAdmin(3));
    }
}
