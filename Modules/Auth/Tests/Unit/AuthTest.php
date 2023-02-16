<?php

namespace Modules\Auth\Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test user can be created
     *
     * @return void
     */
    public function test_user_can_be_created(): void
    {
        $user = new User;
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->password = \Hash::make('password');

        $this->assertTrue($user->save());
    }

    /**
     * Test users password are hashed correctly
     *
     * @return void
     */
    public function test_user_password_hashed_correctly(): void
    {
        $password = 'password';

        $user = new User;
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->password = \Hash::make('password');

        $this->assertTrue(\Hash::check($password, $user->password));
    }

    /**
     * Test user can be authenticated correctly
     *
     * @return void
     */
    public function test_user_can_be_authenticated_correctly(): void
    {
        $this->assertTrue(Auth::attempt(['email' => 'test@example.com', 'password' => 'password']));
    }

    /**
     * Test user cannot be authenticated correctly
     *
     * @return void
     */
    public function test_user_cannot_be_authenticated_correctly(): void
    {
        $this->assertFalse(Auth::attempt(['email' => 'test@example.com', 'password' => 'passwords']));
    }

    /**
     * Test user's auth token has been generated and verified correctly
     *
     * @return void
     */
    public function test_auth_token_generated_and_verified_successfully(): void
    {
        $user = User::first();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token', $response->json());
        $this->assertEquals(Auth::id(), $user->id);
        $token = $response->json()['token_type']. ' '. $response->json()['access_token'];

        $headers = [
            'Authorization' => $token,
            'Accept' => 'application/json',
        ];

        $response = $this->get('/api/user', $headers);

        $response->assertStatus(200);
        $this->assertEquals($user->id, $response->json()['id']);
        $this->assertEquals($user->name, $response->json()['name']);
        $this->assertEquals($user->email, $response->json()['email']);
    }
}
