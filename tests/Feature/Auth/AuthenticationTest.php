<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Giriş Yap');
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_authenticated_users_are_redirected_based_on_role()
    {
        // Test admin redirect
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/home');
        $response->assertRedirect(route('admin.dashboard'));
        
        // Test statistician redirect
        $statistician = User::factory()->create(['role' => 'statistician']);
        $response = $this->actingAs($statistician)->get('/home');
        $response->assertRedirect(route('statistician.dashboard'));
        
        // Test provider redirect
        $provider = User::factory()->create(['role' => 'provider']);
        $response = $this->actingAs($provider)->get('/home');
        $response->assertRedirect(route('provider.dashboard'));
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Kayıt Ol');
    }

    public function test_new_users_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('provider.profile'));
        
        // Check if user was created with provider role
        $user = User::where('email', 'test@example.com')->first();
        $this->assertEquals('provider', $user->role);
    }
}
