<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\IsCustomerAction;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IsCustomerActionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_action_positive()
    {
        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::CUSTOMER)->first()->id
        ]);
        $action = new IsCustomerAction();

        $authManager = app()->get(AuthManager::class);
        $authManager->setUser($user);

        $action($authManager);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_action_negative()
    {
        $this->expectException(HttpException::class);

        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);
        $action = new IsCustomerAction();

        $authManager = app()->get(AuthManager::class);
        $authManager->setUser($user);

        $action($authManager);
    }
}
