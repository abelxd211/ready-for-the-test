<?php

declare(strict_types=1);

final class LabSampleRepository
{
    private const SAMPLE_COLUMNS = 'id, user_id, name, mass, volume, state, created_at FROM lab_samples';

    public function __construct(private readonly PDO $database)
    {
    }

    public function findByUserId(int $userId): array
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::SAMPLE_COLUMNS . ' WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $statement->execute(['user_id' => $userId]);

        return array_map(
            static fn(array $row): LabSample => LabSample::fromRow($row),
            $statement->fetchAll()
        );
    }

    public function findById(int $sampleId): ?LabSample
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::SAMPLE_COLUMNS . ' WHERE id = :id'
        );
        $statement->execute(['id' => $sampleId]);

        $row = $statement->fetch();
        return $row === false ? null : LabSample::fromRow($row);
    }

    public function create(LabSample $sample): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO lab_samples (user_id, name, mass, volume, state) VALUES (:user_id, :name, :mass, :volume, :state)'
        );
        $statement->execute([
            'user_id' => $sample->userId,
            'name' => $sample->name,
            'mass' => $sample->mass,
            'volume' => $sample->volume,
            'state' => $sample->state,
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function countFor(int $userId): int
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*) FROM lab_samples WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }

    public function deleteForUser(int $sampleId, int $userId): bool
    {
        $statement = $this->database->prepare(
            'DELETE FROM lab_samples WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $sampleId,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }
}