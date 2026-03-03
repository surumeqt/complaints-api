<?php

namespace Services;

use Exception;
use Models\UserModel;
use Core\Http\Response;

class AuthService
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register(array $data): void
    {
        $user = $this->userModel->getUserByEmail($data['email']);
        if ($user) throw new Exception('User Exists!', 400);

        $encrypted = encrypt_sensitive_fields($data, ['email']);

        $user = $this->userModel->createUser($encrypted);

        if (!$user) {
            Response::json(['message' => 'Registration failed'], 500);
        }

        generateAndSetToken([
            'id' => $user['id'],
            'role' => $user['role']
        ]);
    }

    public function login(array $data): void
    {
        $user = $this->userModel->getUserByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            Response::json(['message' => 'Invalid credentials'], 401);
        }

        generateAndSetToken([
            'id' => $user['id'],
            'role' => $user['role']
        ]);
    }
}