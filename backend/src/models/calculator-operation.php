<?php

declare(strict_types=1);

final class CalculatorOperation
{
    public const TYPE_BASIC = 'basica';
    public const TYPE_SCIENTIFIC = 'cientifica';

    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $expression,
        public readonly string $result,
        public readonly string $operationType,
        public readonly ?string $createdAt
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['user_id'],
            (string) $row['expression'],
            (string) $row['result'],
            (string) $row['operation_type'],
            (string) $row['created_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'expression' => $this->expression,
            'result' => $this->result,
            'operation_type' => $this->operationType,
            'created_at' => $this->createdAt,
        ];
    }
}