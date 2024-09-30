<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_edit()
    {
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);

        Auth::setUser($userExecutor);

        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertViewIs('components.user.edit-profile');
    }

    public function test_update()
    {
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);

        Auth::setUser($userExecutor);

        $response = $this->patch(route('profile.update'), [
            'name' => $userExecutor->name,
            'mobile' => $userExecutor->mobile,
            'telegram' => '@qwerty123'
        ]);
        $response->assertRedirectToRoute('profile.edit');
    }

    public function test_display()
    {
        /** @var User $userExecutor */
        $userExecutor = User::factory()->create([
            'role_id' => Role::query()->where('name', '=', Role::EXECUTOR)->first()->id
        ]);

        Auth::setUser($userExecutor);

        $response = $this->get(route('profile.display'));
        $response->assertStatus(200);
        $response->assertViewIs('components.user.profile');
    }
}
