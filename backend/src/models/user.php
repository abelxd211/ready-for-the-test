<?php

declare(strict_types=1);

final class User
{
    public const ROLE_STUDENT = 'estudiante';
    public const ROLE_TEACHER = 'docente';

    public function __construct(
        public readonly ?int $id,
        public readonly string $fullName,
        public readonly string $email,
        public readonly string $passwordHash,
        public readonly string $role,
        public readonly int $points
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['full_name'],
            (string) $row['email'],
            (string) $row['password_hash'],
            (string) $row['role'],
            (int) $row['points']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->fullName,
            'email' => $this->email,
            'role' => $this->role,
            'points' => $this->points,
        ];
    }
}