<?php

namespace Tests\Unit\Services;

use App\Repositories\ActiviteDetente\ActiviteRepository;
use App\Services\ActiviteDetente\ActiviteService;
use Mockery;
use PHPUnit\Framework\TestCase;

class ActiviteServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_activites_delegue_au_repository_avec_les_filtres()
    {
        $repository = Mockery::mock(ActiviteRepository::class);
        $repository->shouldReceive('getAllActivites')->once()->with('yoga', 2, 3)->andReturn('collection-filtree');

        $service = new ActiviteService($repository);

        $this->assertSame('collection-filtree', $service->getAllActivites('yoga', 2, 3));
    }

    public function test_toggle_favori_delegue_au_repository()
    {
        $repository = Mockery::mock(ActiviteRepository::class);
        $repository->shouldReceive('toggleFavori')->once()->with(1, 42)->andReturn(['attached' => [42], 'detached' => []]);

        $service = new ActiviteService($repository);

        $result = $service->toggleFavori(1, 42);

        $this->assertSame(['attached' => [42], 'detached' => []], $result);
    }

    public function test_get_progression_retourne_zero_si_aucune_session_n_existe()
    {
        $repository = Mockery::mock(ActiviteRepository::class);
        $repository->shouldReceive('findSession')->once()->with(1, 42)->andReturn(null);

        $service = new ActiviteService($repository);

        $this->assertSame(0, $service->getProgression(1, 42));
    }

    public function test_get_progression_retourne_la_duree_realisee_castee_en_entier()
    {
        $session = (object) ['duree_realisee' => '15'];

        $repository = Mockery::mock(ActiviteRepository::class);
        $repository->shouldReceive('findSession')->once()->with(1, 42)->andReturn($session);

        $service = new ActiviteService($repository);

        $this->assertSame(15, $service->getProgression(1, 42));
    }

    public function test_save_progression_delegue_au_repository()
    {
        $repository = Mockery::mock(ActiviteRepository::class);
        $repository->shouldReceive('updateOrCreateSession')->once()->with(1, 42, 20, true);

        $service = new ActiviteService($repository);

        $service->saveProgression(1, 42, 20, true);

        $this->addToAssertionCount(1);
    }

    public function test_desactiver_activite_delegue_au_repository()
    {
        $repository = Mockery::mock(ActiviteRepository::class);
        $repository->shouldReceive('disableActivite')->once()->with(7)->andReturn('activite-desactivee');

        $service = new ActiviteService($repository);

        $this->assertSame('activite-desactivee', $service->desactiverActivite(7));
    }
}
