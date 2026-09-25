<?php

declare(strict_types=1);

final class LabSampleController
{
    public function __construct(private readonly LabSampleService $service)
    {
    }

    public function list(User $user): void
    {
        $samples = $this->service->listSamples($user->id);
        jsonResponse(200, true, $samples, 'Muestras obtenidas');
    }

    public function create(User $user): void
    {
        $requestBody = readJsonBody();
        $result = $this->service->registerSample(
            $user->id,
            $requestBody['name'] ?? '',
            (float) ($requestBody['mass'] ?? 0),
            (float) ($requestBody['volume'] ?? 0),
            $requestBody['state'] ?? ''
        );

        jsonResponse(201, true, $result, 'Muestra registrada');
    }

    public function delete(User $user, int $sampleId): void
    {
        $this->service->deleteSample($user->id, $sampleId);
        jsonResponse(200, true, null, 'Muestra eliminada');
    }
}