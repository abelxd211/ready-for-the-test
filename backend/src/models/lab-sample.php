<?php

declare(strict_types=1);

final class LabSample
{
    private const DENSITY_DECIMALS = 3;

    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $name,
        public readonly float $mass,
        public readonly float $volume,
        public readonly string $state,
        public readonly ?string $createdAt
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['user_id'],
            (string) $row['name'],
            (float) $row['mass'],
            (float) $row['volume'],
            (string) $row['state'],
            (string) $row['created_at']
        );
    }

    public function density(): float
    {
        if ($this->volume <= 0.0) {
            return 0.0;
        }

        return round($this->mass / $this->volume, self::DENSITY_DECIMALS);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mass' => $this->mass,
            'volume' => $this->volume,
            'state' => $this->state,
            'density' => $this->density(),
            'created_at' => $this->createdAt,
        ];
    }
}