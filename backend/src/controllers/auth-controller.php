<?php

declare(strict_types=1);

final class AuthController
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function register(): void
    {
        $requestBody = readJsonBody();
        $user = $this->authService->register(
            $requestBody['full_name'] ?? '',
            $requestBody['email'] ?? '',
            $requestBody['password'] ?? '',
            $requestBody['role'] ?? User::ROLE_STUDENT
        );

        jsonResponse(201, true, [
            'token' => $this->authService->issueToken($user),
            'user' => $user->toArray(),
        ], 'Registro exitoso');
    }

    public function login(): void
    {
        $requestBody = readJsonBody();
        $result = $this->authService->login(
            $requestBody['email'] ?? '',
            $requestBody['password'] ?? ''
        );

        jsonResponse(200, true, $result, 'Inicio de sesión exitoso');
    }

    public function me(User $user): void
    {
        jsonResponse(200, true, $user->toArray(), 'Sesión activa');
    }
}