<?php

declare(strict_types=1);

final class LabExperimentRepository
{
    private const MAX_RESULTS = 50;
    private const EXPERIMENT_COLUMNS = 'id, user_id, sample_id, experiment_type, result_summary, points_awarded, created_at FROM lab_experiments';

    public function __construct(private readonly PDO $database)
    {
    }

    public function findByUserId(int $userId): array
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::EXPERIMENT_COLUMNS . ' WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit'
        );
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', self::MAX_RESULTS, PDO::PARAM_INT);
        $statement->execute();

        return array_map(
            static fn(array $row): LabExperiment => LabExperiment::fromRow($row),
            $statement->fetchAll()
        );
    }

    public function findById(int $experimentId): ?LabExperiment
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::EXPERIMENT_COLUMNS . ' WHERE id = :id'
        );
        $statement->execute(['id' => $experimentId]);

        $row = $statement->fetch();
        return $row === false ? null : LabExperiment::fromRow($row);
    }

    public function countFor(int $userId): int
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*) FROM lab_experiments WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }

    public function create(LabExperiment $experiment): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO lab_experiments (user_id, sample_id, experiment_type, result_summary, points_awarded) VALUES (:user_id, :sample_id, :experiment_type, :result_summary, :points_awarded)'
        );
        $statement->execute([
            'user_id' => $experiment->userId,
            'sample_id' => $experiment->sampleId,
            'experiment_type' => $experiment->experimentType,
            'result_summary' => $experiment->resultSummary,
            'points_awarded' => $experiment->pointsAwarded,
        ]);

        return (int) $this->database->lastInsertId();
    }
}