<?php

declare(strict_types=1);

final class LabExperiment
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly ?int $sampleId,
        public readonly string $experimentType,
        public readonly string $resultSummary,
        public readonly int $pointsAwarded,
        public readonly ?string $createdAt
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['user_id'],
            $row['sample_id'] === null ? null : (int) $row['sample_id'],
            (string) $row['experiment_type'],
            (string) $row['result_summary'],
            (int) $row['points_awarded'],
            (string) $row['created_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sample_id' => $this->sampleId,
            'experiment_type' => $this->experimentType,
            'result_summary' => $this->resultSummary,
            'points_awarded' => $this->pointsAwarded,
            'created_at' => $this->createdAt,
        ];
    }
}