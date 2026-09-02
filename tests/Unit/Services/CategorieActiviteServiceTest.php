<?php

namespace Tests\Unit\Services;

use App\Repositories\Informations\CategorieActiviteRepository;
use App\Services\Informations\CategorieActiviteService;
use Mockery;
use PHPUnit\Framework\TestCase;

class CategorieActiviteServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_categories_delegue_au_repository()
    {
        $repository = Mockery::mock(CategorieActiviteRepository::class);
        $repository->shouldReceive('getAllCategories')->once()->andReturn('liste-categories');

        $service = new CategorieActiviteService($repository);

        $this->assertSame('liste-categories', $service->getAllCategories());
    }

    public function test_delete_categorie_activite_retourne_has_relations_si_liee()
    {
        $repository = Mockery::mock(CategorieActiviteRepository::class);
        $repository->shouldReceive('deleteCategorieActivite')->once()->with(3)->andReturn('HAS_RELATIONS');

        $service = new CategorieActiviteService($repository);

        $this->assertSame('HAS_RELATIONS', $service->deleteCategorieActivite(3));
    }

    public function test_add_categorie_activite_delegue_au_repository()
    {
        $data = ['libelle_categorie' => 'Sommeil'];

        $repository = Mockery::mock(CategorieActiviteRepository::class);
        $repository->shouldReceive('AddCategorieActivite')->once()->with($data)->andReturn('categorie-creee');

        $service = new CategorieActiviteService($repository);

        $this->assertSame('categorie-creee', $service->AddCategorieActivite($data));
    }
}
