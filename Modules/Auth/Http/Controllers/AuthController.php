<?php

namespace Modules\Auth\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Exceptions\AuthException;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Services\AuthServices;

class AuthController extends Controller
{
    /**
     * @param  AuthServices  $authServices
     */
    public function __construct(private AuthServices $authServices) {}

    /**
     * Register function for the user
     *
     * @param  RegisterRequest  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $token = $this->authServices->handleRegister($request->only('name', 'email', 'password'));

        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'message'       => 'User created successfully'
        ]);
    }

    /**
     * Login function for the user
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     * @throws AuthException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authServices->handleLogin($request->only('email', 'password'));

        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'Bearer',
        ]);
    }

    /**
     * Logout function for the user
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authServices->handleLogout();

        return response()->json([
            'message'  => 'User logged out successfully'
        ]);
    }
}
