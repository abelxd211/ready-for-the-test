<?php

declare(strict_types=1);

final class LabExperimentController
{
    public function __construct(private readonly LabExperimentService $service)
    {
    }

    public function list(User $user): void
    {
        $experiments = $this->service->listFor($user->id);
        jsonResponse(200, true, $experiments, 'Historial de experimentos');
    }
}