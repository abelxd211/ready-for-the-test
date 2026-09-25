<?php

declare(strict_types=1);

final class DiscoveredReactionRepository
{
    public function __construct(private readonly PDO $database)
    {
    }

    public function has(int $userId, int $reactionId): bool
    {
        $statement = $this->database->prepare(
            'SELECT 1 FROM discovered_reactions WHERE user_id = :user_id AND reaction_id = :reaction_id LIMIT 1'
        );
        $statement->execute([
            'user_id' => $userId,
            'reaction_id' => $reactionId,
        ]);

        return $statement->fetch() !== false;
    }

    public function countFor(int $userId): int
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*) FROM discovered_reactions WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }

    public function create(int $userId, int $reactionId): void
    {
        $statement = $this->database->prepare(
            'INSERT INTO discovered_reactions (user_id, reaction_id) VALUES (:user_id, :reaction_id)'
        );
        $statement->execute([
            'user_id' => $userId,
            'reaction_id' => $reactionId,
        ]);
    }
}