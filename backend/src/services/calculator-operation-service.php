<?php

declare(strict_types=1);

final class CalculatorOperationService
{
    private const MAX_EXPRESSION_LENGTH = 255;
    private const MAX_RESULT_LENGTH = 60;

    public function __construct(private readonly CalculatorOperationRepository $repository)
    {
    }

    public function listFor(int $userId): array
    {
        $operations = $this->repository->findByUserId($userId);

        return array_map(
            static fn(CalculatorOperation $operation): array => $operation->toArray(),
            $operations
        );
    }

    public function record(int $userId, string $expression, string $result, string $operationType): CalculatorOperation
    {
        $this->assertValidEntry($expression, $result, $operationType);
        $operation = new CalculatorOperation(null, $userId, $expression, $result, $operationType, null);
        $operationId = $this->repository->create($operation);

        return $this->repository->findById($operationId) ?? $operation;
    }

    private function assertValidEntry(string $expression, string $result, string $operationType): void
    {
        if (trim($expression) === '' || strlen($expression) > self::MAX_EXPRESSION_LENGTH) {
            throw new ValidationException('La expresión es obligatoria (máximo 255 caracteres)');
        }
        if (trim($result) === '' || strlen($result) > self::MAX_RESULT_LENGTH) {
            throw new ValidationException('El resultado es obligatorio (máximo 60 caracteres)');
        }

        $validTypes = [CalculatorOperation::TYPE_BASIC, CalculatorOperation::TYPE_SCIENTIFIC];
        if (!in_array($operationType, $validTypes, true)) {
            throw new ValidationException('El tipo de operación debe ser basica o cientifica');
        }
    }
}