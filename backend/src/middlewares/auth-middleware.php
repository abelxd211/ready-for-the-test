<?php

declare(strict_types=1);

final class AuthMiddleware
{
    private const BEARER_PREFIX = 'Bearer ';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly string $tokenSecret
    ) {
    }

    public function authenticate(): User
    {
        $token = $this->extractBearerToken();
        $payload = Token::decode($token, $this->tokenSecret);
        if ($payload === null || !isset($payload['user_id'])) {
            throw new UnauthorizedException('Token inválido o expirado');
        }

        $user = $this->userRepository->findById((int) $payload['user_id']);
        if ($user === null) {
            throw new UnauthorizedException('Usuario no encontrado');
        }

        return $user;
    }

    private function extractBearerToken(): string
    {
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!str_starts_with($authorizationHeader, self::BEARER_PREFIX)) {
            return '';
        }

        return substr($authorizationHeader, strlen(self::BEARER_PREFIX));
    }
}