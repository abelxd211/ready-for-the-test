<?php

declare(strict_types=1);

final class MixtureReaction
{
    public function __construct(
        public readonly int $id,
        public readonly string $reagentAId,
        public readonly string $reagentBId,
        public readonly string $resultName,
        public readonly string $description,
        public readonly string $effect,
        public readonly string $resultColorHex
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['reagent_a_id'],
            (string) $row['reagent_b_id'],
            (string) $row['result_name'],
            (string) $row['description'],
            (string) $row['effect'],
            (string) $row['result_color_hex']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'reagent_a_id' => $this->reagentAId,
            'reagent_b_id' => $this->reagentBId,
            'result_name' => $this->resultName,
            'description' => $this->description,
            'effect' => $this->effect,
            'result_color_hex' => $this->resultColorHex,
        ];
    }
}