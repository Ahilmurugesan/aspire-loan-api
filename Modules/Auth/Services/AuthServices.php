<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Exceptions\AuthException;

class AuthServices
{
    /**
     * Function to register and authenticate the user
     *
     * @param  array  $credentials
     * @return mixed
     * @throws AuthException
     */
    public function handleRegister(array $credentials): mixed
    {
        try {

            $user = User::create([
                'name' => $credentials['name'],
                'email' => $credentials['email'],
                'password' => Hash::make($credentials['password'])
            ]);

            return static::createAuthToken($user);

        } catch(Exception $e) {

            Log::error($e->getMessage());

            throw new AuthException('User registration failed.');
        }
    }

    /**
     * Function to authenticate the user
     *
     * @param  array  $credentials
     * @return mixed
     * @throws AuthException
     */
    public function handleLogin(array $credentials): mixed
    {
        if (!Auth::attempt($credentials)) {
            throw new AuthException('Invalid login details');
        }

        try {

            $user = User::where('email', $credentials['email'])->firstOrFail();

            return static::createAuthToken($user);

        } catch(Exception $e) {

            Log::error($e->getMessage());

            throw new AuthException('User login failed.');
        }
    }

    /**
     * Function to handle the logout
     *
     * @return true
     */
    public function handleLogout(): bool
    {
        Auth::user()->currentAccessToken()->delete();

        return true;
    }

    /**
     * Function to create the auth token
     *
     * @param  object  $user
     * @return mixed
     */
    private static function createAuthToken(object $user): mixed
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}
