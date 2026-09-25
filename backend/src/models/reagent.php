<?php

declare(strict_types=1);

final class Reagent
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $colorHex,
        public readonly string $icon,
        public readonly string $description
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (string) $row['id'],
            (string) $row['name'],
            (string) $row['color_hex'],
            (string) $row['icon'],
            (string) $row['description']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color_hex' => $this->colorHex,
            'icon' => $this->icon,
            'description' => $this->description,
        ];
    }
}