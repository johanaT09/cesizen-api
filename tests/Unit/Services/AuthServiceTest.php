<?php

namespace Tests\Unit\Services;

use App\Repositories\Compte\AuthRepository;
use App\Services\Compte\AuthService;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_login_lance_une_exception_si_l_utilisateur_n_existe_pas()
    {
        $repository = Mockery::mock(AuthRepository::class);
        $repository->shouldReceive('findByEmail')->once()->with('inconnu@cesizen.fr')->andReturn(null);

        $service = new AuthService($repository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Identifiants invalides');

        $service->login('inconnu@cesizen.fr', 'peu-importe');
    }

    public function test_login_lance_une_exception_si_le_compte_est_desactive()
    {
        $user = Mockery::mock();
        $user->est_actif = false;
        $user->mot_de_passe = 'hash';

        $repository = Mockery::mock(AuthRepository::class);
        $repository->shouldReceive('findByEmail')->once()->andReturn($user);

        $service = new AuthService($repository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ce compte a été désactivé. Veuillez contacter l\'administrateur.');

        $service->login('desactive@cesizen.fr', 'peu-importe');
    }

    public function test_login_lance_une_exception_si_le_mot_de_passe_est_incorrect()
    {
        $user = Mockery::mock();
        $user->est_actif = true;
        $user->mot_de_passe = password_hash('BonMotDePasse', PASSWORD_BCRYPT);

        $repository = Mockery::mock(AuthRepository::class);
        $repository->shouldReceive('findByEmail')->once()->andReturn($user);

        $service = new AuthService($repository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Identifiants invalides');

        $service->login('utilisateur@cesizen.fr', 'MauvaisMotDePasse');
    }

    public function test_login_retourne_l_utilisateur_et_un_token_si_les_identifiants_sont_corrects()
    {
        $tokenModel = Mockery::mock(PersonalAccessToken::class)->makePartial();
        $tokenModel->shouldReceive('save')->once()->andReturn(true);

        $tokenResult = Mockery::mock(NewAccessToken::class);
        $tokenResult->plainTextToken = 'plain-text-token';
        $tokenResult->accessToken = $tokenModel;

        $user = Mockery::mock();
        $user->est_actif = true;
        $user->mot_de_passe = password_hash('BonMotDePasse', PASSWORD_BCRYPT);
        $user->shouldReceive('createToken')->once()->with('auth_token')->andReturn($tokenResult);

        $repository = Mockery::mock(AuthRepository::class);
        $repository->shouldReceive('findByEmail')->once()->with('utilisateur@cesizen.fr')->andReturn($user);

        $service = new AuthService($repository);

        $result = $service->login('utilisateur@cesizen.fr', 'BonMotDePasse');

        $this->assertSame($user, $result['user']);
        $this->assertSame('plain-text-token', $result['token']);
    }

    public function test_logout_supprime_le_token_courant()
    {
        $token = Mockery::mock();
        $token->shouldReceive('delete')->once()->andReturn(true);

        $user = Mockery::mock();
        $user->shouldReceive('currentAccessToken')->once()->andReturn($token);

        $repository = Mockery::mock(AuthRepository::class);
        $service = new AuthService($repository);

        $result = $service->logout($user);

        $this->assertTrue($result);
    }
}
