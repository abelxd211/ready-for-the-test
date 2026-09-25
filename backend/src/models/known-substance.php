<?php

declare(strict_types=1);

final class KnownSubstance
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $state,
        public readonly string $transparency,
        public readonly string $conductivity,
        public readonly float $density,
        public readonly string $detail
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['description'],
            (string) $row['state'],
            (string) $row['transparency'],
            (string) $row['conductivity'],
            (float) $row['density'],
            (string) $row['detail']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'state' => $this->state,
            'transparency' => $this->transparency,
            'conductivity' => $this->conductivity,
            'density' => $this->density,
            'detail' => $this->detail,
        ];
    }
}