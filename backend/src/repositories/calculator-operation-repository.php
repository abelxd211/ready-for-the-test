<?php

declare(strict_types=1);

final class CalculatorOperationRepository
{
    private const MAX_RESULTS = 50;
    private const OPERATION_COLUMNS = 'id, user_id, expression, result, operation_type, created_at FROM calculator_operations';

    public function __construct(private readonly PDO $database)
    {
    }

    public function findByUserId(int $userId): array
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::OPERATION_COLUMNS . ' WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit'
        );
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', self::MAX_RESULTS, PDO::PARAM_INT);
        $statement->execute();

        return array_map(
            static fn(array $row): CalculatorOperation => CalculatorOperation::fromRow($row),
            $statement->fetchAll()
        );
    }

    public function findById(int $operationId): ?CalculatorOperation
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::OPERATION_COLUMNS . ' WHERE id = :id'
        );
        $statement->execute(['id' => $operationId]);

        $row = $statement->fetch();
        return $row === false ? null : CalculatorOperation::fromRow($row);
    }

    public function countFor(int $userId): int
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*) FROM calculator_operations WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }

    public function create(CalculatorOperation $operation): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO calculator_operations (user_id, expression, result, operation_type) VALUES (:user_id, :expression, :result, :operation_type)'
        );
        $statement->execute([
            'user_id' => $operation->userId,
            'expression' => $operation->expression,
            'result' => $operation->result,
            'operation_type' => $operation->operationType,
        ]);

        return (int) $this->database->lastInsertId();
    }
}