<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ViewUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_all_users()
    {
        $users = create(User::class, 5);

        $this->signIn($users[0], 'office');

        $response = $this->withoutExceptionHandling()
            ->get('/users')
            ->assertSuccessful()
            ->assertViewIs('users.index')
            ->assertViewHasAll(['users', 'roles']);

        $this->assertCount(User::count(), $response->viewData('users'));
        $this->assertCount(Role::count(), $response->viewData('roles'));
    }
}