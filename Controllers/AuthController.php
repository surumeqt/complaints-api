<?php

namespace Controllers;

use Core\Http\Request;
use Core\Http\Response;
use Services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => true,
            'email' => true,
            'password' => true
        ]);

        $this->authService->register($data);

        Response::json(['message' => 'Registration successful'], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => true,
            'password' => true
        ]);

        $this->authService->login($data);

        Response::json(['message' => 'Login successful']);
    }
    public function logout()
    {
        clearTokenCookie();
        Response::noContent();
    }
}