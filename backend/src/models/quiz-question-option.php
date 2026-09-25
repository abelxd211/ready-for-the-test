<?php

declare(strict_types=1);

final class QuizQuestionOption
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $questionId,
        public readonly int $position,
        public readonly string $optionText
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['question_id'],
            (int) $row['option_position'],
            (string) $row['option_text']
        );
    }
}