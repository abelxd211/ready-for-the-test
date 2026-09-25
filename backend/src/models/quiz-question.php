<?php

declare(strict_types=1);

final class QuizQuestion
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $roomId,
        public readonly string $questionText,
        public readonly int $correctOption,
        public readonly int $pointsValue
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['room_id'],
            (string) $row['question_text'],
            (int) $row['correct_option'],
            (int) $row['points_value']
        );
    }
}