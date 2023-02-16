<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test to find whether the user can register successfully
     *
     * @return void
     */
    public function test_user_can_register(): void
    {
        $response = $this->post('/api/register', [
            'name' => 'Test User',
            'email' => 'register@example.com',
            'password' => 'password'
        ]);

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'message'
        ]);
    }

    /**
     * @return void
     */
    public function test_user_cannot_register_with_invalid_credentials(): void
    {
        $response = $this->post( '/api/register', [
            'name' => '',
            'email' => 'register2@example.com',
            'password' => '',
        ],['Accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'name'    => ['The name field is required.'],
                'password' => ['The password field is required.'],
            ],
        ]);
        $this->assertGuest();
    }

    /**
     * Function to test user can log in successfully
     *
     * @return void
     */
    public function test_user_can_login(): void
    {
        $user = User::first();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type'
        ]);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Function to test user cannot log in with invalid credentials
     *
     * @return void
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::first();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'wrongPassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid login details',
        ]);
        $this->assertGuest();
    }

    /**
     * Test usr cannot access the protected pages without token
     *
     * @return void
     */
    public function test_user_cannot_access_protected_pages_without_valid_token(): void
    {
        $response = $this->get('/api/user', ['Accept' => 'application/json']);

        $response->assertStatus(401);
        $this->assertFalse(Auth::check());
    }
}
