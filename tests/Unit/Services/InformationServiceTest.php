<?php

namespace Tests\Unit\Services;

use App\Repositories\Informations\InformationRepository;
use App\Services\Informations\InformationService;
use Mockery;
use PHPUnit\Framework\TestCase;

class InformationServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_informations_delegue_au_repository()
    {
        $repository = Mockery::mock(InformationRepository::class);
        $repository->shouldReceive('getAllInformations')->once()->with('stress', 2)->andReturn('liste-informations');

        $service = new InformationService($repository);

        $this->assertSame('liste-informations', $service->getAllInformations('stress', 2));
    }

    public function test_create_information_delegue_au_repository()
    {
        $data = ['titre_information' => 'Titre', 'id_categorie' => 1];

        $repository = Mockery::mock(InformationRepository::class);
        $repository->shouldReceive('create')->once()->with($data)->andReturn('information-creee');

        $service = new InformationService($repository);

        $this->assertSame('information-creee', $service->createInformation($data));
    }

    public function test_update_information_delegue_au_repository()
    {
        $data = ['titre_information' => 'Nouveau titre'];

        $repository = Mockery::mock(InformationRepository::class);
        $repository->shouldReceive('update')->once()->with(5, $data)->andReturn('information-maj');

        $service = new InformationService($repository);

        $this->assertSame('information-maj', $service->updateInformation(5, $data));
    }

    public function test_toggle_status_delegue_au_repository()
    {
        $repository = Mockery::mock(InformationRepository::class);
        $repository->shouldReceive('toggleStatus')->once()->with(9)->andReturn('information-basculee');

        $service = new InformationService($repository);

        $this->assertSame('information-basculee', $service->toggleStatus(9));
    }
}
