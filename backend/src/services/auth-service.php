<?php

declare(strict_types=1);

final class AuthService
{
    public const TOKEN_TTL_SECONDS = 43200;
    public const DEFAULT_POINTS = 0;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly string $tokenSecret
    ) {
    }

    public function register(string $fullName, string $email, string $password, string $role): User
    {
        $normalizedEmail = $this->normalizeEmail($email);
        $this->assertValidRegistration($fullName, $normalizedEmail, $password, $role);

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $user = new User(null, trim($fullName), $normalizedEmail, $passwordHash, $role, self::DEFAULT_POINTS);
        $userId = $this->userRepository->create($user);

        return new User($userId, $user->fullName, $normalizedEmail, $passwordHash, $role, self::DEFAULT_POINTS);
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($this->normalizeEmail($email));
        if ($user === null || !password_verify($password, $user->passwordHash)) {
            throw new UnauthorizedException('Credenciales inválidas');
        }

        return [
            'token' => $this->issueToken($user),
            'user' => $user->toArray(),
        ];
    }

    public function issueToken(User $user): string
    {
        return Token::encode($user->id ?? 0, $user->role, self::TOKEN_TTL_SECONDS, $this->tokenSecret);
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function assertValidRegistration(string $fullName, string $email, string $password, string $role): void
    {
        if (!isValidFullName($fullName)) {
            throw new ValidationException('El nombre completo es obligatorio (máximo 120 caracteres)');
        }
        if (!isValidEmail($email)) {
            throw new ValidationException('Correo electrónico inválido');
        }
        if (!isValidPassword($password)) {
            throw new ValidationException('La contraseña debe tener al menos 8 caracteres');
        }
        if (!isValidRole($role)) {
            throw new ValidationException('El rol debe ser estudiante o docente');
        }
        if ($this->userRepository->findByEmail($email) !== null) {
            throw new ValidationException('El correo ya está registrado');
        }
    }
}