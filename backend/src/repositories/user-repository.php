<?php

declare(strict_types=1);

final class UserRepository
{
    private const USER_COLUMNS = 'id, full_name, email, password_hash, role, points FROM users';

    public function __construct(private readonly PDO $database)
    {
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::USER_COLUMNS . ' WHERE email = :email'
        );
        $statement->execute(['email' => $email]);

        $row = $statement->fetch();
        return $row === false ? null : User::fromRow($row);
    }

    public function findById(int $userId): ?User
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::USER_COLUMNS . ' WHERE id = :id'
        );
        $statement->execute(['id' => $userId]);

        $row = $statement->fetch();
        return $row === false ? null : User::fromRow($row);
    }

    public function create(User $user): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO users (full_name, email, password_hash, role) VALUES (:full_name, :email, :password_hash, :role)'
        );
        $statement->execute([
            'full_name' => $user->fullName,
            'email' => $user->email,
            'password_hash' => $user->passwordHash,
            'role' => $user->role,
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function addPoints(int $userId, int $points): void
    {
        $statement = $this->database->prepare(
            'UPDATE users SET points = points + :points WHERE id = :id'
        );
        $statement->execute([
            'points' => $points,
            'id' => $userId,
        ]);
    }
}