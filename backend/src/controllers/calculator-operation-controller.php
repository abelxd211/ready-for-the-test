<?php

declare(strict_types=1);

final class CalculatorOperationController
{
    public function __construct(private readonly CalculatorOperationService $service)
    {
    }

    public function list(User $user): void
    {
        $operations = $this->service->listFor($user->id);
        jsonResponse(200, true, $operations, 'Historial obtenido');
    }

    public function create(User $user): void
    {
        $requestBody = readJsonBody();
        $operation = $this->service->record(
            $user->id,
            $requestBody['expression'] ?? '',
            $requestBody['result'] ?? '',
            $requestBody['operation_type'] ?? CalculatorOperation::TYPE_BASIC
        );

        jsonResponse(201, true, $operation->toArray(), 'Operación registrada');
    }
}