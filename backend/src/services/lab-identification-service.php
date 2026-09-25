<?php

declare(strict_types=1);

final class LabIdentificationService
{
    private const VALID_STATES = ['solido', 'liquido', 'gas'];
    private const VALID_TRANSPARENCIES = ['transparente', 'opaco'];
    private const VALID_CONDUCTIVITIES = ['conduce', 'noconduce'];

    public function __construct(
        private readonly KnownSubstanceRepository $substanceRepository,
        private readonly LabExperimentService $experimentService
    ) {
    }

    public function identify(int $userId, string $state, string $transparency, string $conductivity, float $density): array
    {
        $this->assertValidProperties($state, $transparency, $conductivity, $density);
        $substance = $this->nearestByDensity(
            $this->substanceRepository->findByProperties($state, $transparency, $conductivity),
            $density
        );

        if ($substance === null) {
            $this->experimentService->recordActivity($userId, 'identificacion', 'Sustancia no identificada', null, 0);

            return ['substance' => null, 'points_awarded' => 0];
        }

        $points = LabExperimentService::EXPERIMENT_BASE_POINTS;
        $this->experimentService->recordActivity($userId, 'identificacion', 'Identificaste: ' . $substance->name, null, $points);

        return ['substance' => $substance->toArray(), 'points_awarded' => $points];
    }

    private function assertValidProperties(string $state, string $transparency, string $conductivity, float $density): void
    {
        if (!in_array($state, self::VALID_STATES, true)) {
            throw new ValidationException('El estado debe ser solido, liquido o gas');
        }
        if (!in_array($transparency, self::VALID_TRANSPARENCIES, true)) {
            throw new ValidationException('La transparencia debe ser transparente u opaco');
        }
        if (!in_array($conductivity, self::VALID_CONDUCTIVITIES, true)) {
            throw new ValidationException('La conductividad debe ser conduce o noconduce');
        }
        if ($density <= 0) {
            throw new ValidationException('La densidad debe ser un valor positivo');
        }
    }

    private function nearestByDensity(array $candidates, float $density): ?KnownSubstance
    {
        $nearest = null;
        $nearestDiff = PHP_FLOAT_MAX;

        foreach ($candidates as $candidate) {
            $diff = abs($candidate->density - $density);
            if ($diff < $nearestDiff) {
                $nearestDiff = $diff;
                $nearest = $candidate;
            }
        }

        return $nearest;
    }
}