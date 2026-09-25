<?php

declare(strict_types=1);

final class LabIdentificationController
{
    public function __construct(private readonly LabIdentificationService $service)
    {
    }

    public function identify(User $user): void
    {
        $requestBody = readJsonBody();
        $result = $this->service->identify(
            $user->id,
            $requestBody['state'] ?? '',
            $requestBody['transparency'] ?? '',
            $requestBody['conductivity'] ?? '',
            (float) ($requestBody['density'] ?? 0)
        );

        jsonResponse(200, true, $result, 'Identificación completada');
    }
}