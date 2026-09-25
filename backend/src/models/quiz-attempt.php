<?php

declare(strict_types=1);

final class QuizAttempt
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $roomId,
        public readonly int $userId,
        public readonly int $score,
        public readonly int $totalQuestions,
        public readonly ?string $finishedAt,
        public readonly ?string $createdAt
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['room_id'],
            (int) $row['user_id'],
            (int) $row['score'],
            (int) $row['total_questions'],
            $row['finished_at'] === null ? null : (string) $row['finished_at'],
            (string) $row['created_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'room_id' => $this->roomId,
            'score' => $this->score,
            'total_questions' => $this->totalQuestions,
            'finished_at' => $this->finishedAt,
            'created_at' => $this->createdAt,
        ];
    }
}