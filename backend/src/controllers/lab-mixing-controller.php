<?php

declare(strict_types=1);

final class LabMixingController
{
    public function __construct(private readonly LabMixingService $service)
    {
    }

    public function mix(User $user): void
    {
        $requestBody = readJsonBody();
        $result = $this->service->mix(
            $user->id,
            $requestBody['reagent_a_id'] ?? '',
            $requestBody['reagent_b_id'] ?? ''
        );

        jsonResponse(201, true, $result, 'Mezcla realizada');
    }
}