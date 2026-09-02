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
