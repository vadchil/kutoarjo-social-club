<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin');
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_active' => false,
        ]);

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_staff_cannot_access_pages_requiring_admin_role_if_not_permitted(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        // Dashboard is shared (admin, staff), but let's test a hypothetical admin-only route
        $this->actingAs($user);

        // Define a temp admin route for verification
        $this->app['router']->get('/admin/test-only', function () {
            return 'ok';
        })->middleware(['web', 'auth', 'role:admin']);

        $response = $this->get('/admin/test-only');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_pages_requiring_admin_role(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $this->app['router']->get('/admin/test-only', function () {
            return 'ok';
        })->middleware(['web', 'auth', 'role:admin']);

        $response = $this->get('/admin/test-only');
        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_admin_cms_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $this->get('/admin/settings')->assertStatus(403);
        $this->get('/admin/gallery')->assertStatus(403);
        $this->get('/admin/faqs')->assertStatus(403);
    }
}
