<?php

declare(strict_types=1);

final class QuizRoom
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $creatorId,
        public readonly string $title,
        public readonly string $code,
        public readonly string $status,
        public readonly ?string $createdAt
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['creator_id'],
            (string) $row['title'],
            (string) $row['code'],
            (string) $row['status'],
            (string) $row['created_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'status' => $this->status,
            'created_at' => $this->createdAt,
        ];
    }
}