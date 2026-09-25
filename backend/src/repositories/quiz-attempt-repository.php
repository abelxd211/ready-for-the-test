<?php

declare(strict_types=1);

final class QuizAttemptRepository
{
    private const ATTEMPT_COLUMNS = 'id, room_id, user_id, score, total_questions, finished_at, created_at FROM quiz_attempts';

    public function __construct(private readonly PDO $database)
    {
    }

    public function create(QuizAttempt $attempt): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO quiz_attempts (room_id, user_id, total_questions) VALUES (:room_id, :user_id, :total_questions)'
        );
        $statement->execute([
            'room_id' => $attempt->roomId,
            'user_id' => $attempt->userId,
            'total_questions' => $attempt->totalQuestions,
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function findById(int $attemptId): ?QuizAttempt
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::ATTEMPT_COLUMNS . ' WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $attemptId]);

        $row = $statement->fetch();
        return $row === false ? null : QuizAttempt::fromRow($row);
    }

    public function findByRoomAndUser(int $roomId, int $userId): ?QuizAttempt
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::ATTEMPT_COLUMNS . ' WHERE room_id = :room_id AND user_id = :user_id LIMIT 1'
        );
        $statement->execute([
            'room_id' => $roomId,
            'user_id' => $userId,
        ]);

        $row = $statement->fetch();
        return $row === false ? null : QuizAttempt::fromRow($row);
    }

    public function findByUserId(int $userId): array
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::ATTEMPT_COLUMNS . ' WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $statement->execute(['user_id' => $userId]);

        return array_map(
            static fn(array $row): QuizAttempt => QuizAttempt::fromRow($row),
            $statement->fetchAll()
        );
    }

    public function countFinishedFor(int $userId): int
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*) FROM quiz_attempts WHERE user_id = :user_id AND finished_at IS NOT NULL'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }

    public function finish(int $attemptId, int $score): void
    {
        $statement = $this->database->prepare(
            'UPDATE quiz_attempts SET score = :score, finished_at = CURRENT_TIMESTAMP WHERE id = :id'
        );
        $statement->execute([
            'score' => $score,
            'id' => $attemptId,
        ]);
    }
}