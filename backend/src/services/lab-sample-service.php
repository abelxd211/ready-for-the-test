<?php

declare(strict_types=1);

final class LabSampleService
{
    private const MAX_NAME_LENGTH = 80;
    private const MAX_MEASUREMENT = 9999999.0;
    private const VALID_STATES = ['solido', 'liquido', 'gas'];

    public function __construct(
        private readonly LabSampleRepository $sampleRepository,
        private readonly LabExperimentService $experimentService
    ) {
    }

    public function listSamples(int $userId): array
    {
        $samples = $this->sampleRepository->findByUserId($userId);

        return array_map(
            static fn(LabSample $sample): array => $sample->toArray(),
            $samples
        );
    }

    public function registerSample(int $userId, string $name, float $mass, float $volume, string $state): array
    {
        $this->assertValidSample($name, $mass, $volume, $state);
        $sample = new LabSample(null, $userId, trim($name), $mass, $volume, $state, null);
        $sampleId = $this->sampleRepository->create($sample);
        $createdSample = $this->sampleRepository->findById($sampleId);

        $summary = sprintf('Muestra registrada: %s, densidad %.3f g/mL', $createdSample->name, $createdSample->density());
        $experiment = $this->experimentService->recordActivity(
            $userId,
            'densidad',
            $summary,
            $sampleId,
            LabExperimentService::EXPERIMENT_BASE_POINTS
        );

        return [
            'sample' => $createdSample->toArray(),
            'density' => $createdSample->density(),
            'points_awarded' => $experiment->pointsAwarded,
        ];
    }

    public function deleteSample(int $userId, int $sampleId): void
    {
        $sample = $this->sampleRepository->findById($sampleId);
        if ($sample === null || $sample->userId !== $userId) {
            throw new NotFoundException('Muestra no encontrada');
        }

        $this->sampleRepository->deleteForUser($sampleId, $userId);
    }

    private function assertValidSample(string $name, float $mass, float $volume, string $state): void
    {
        $trimmedName = trim($name);
        if ($trimmedName === '' || strlen($trimmedName) > self::MAX_NAME_LENGTH) {
            throw new ValidationException('El nombre es obligatorio (máximo 80 caracteres)');
        }
        if ($mass <= 0 || $mass > self::MAX_MEASUREMENT) {
            throw new ValidationException('La masa debe ser un valor positivo');
        }
        if ($volume <= 0 || $volume > self::MAX_MEASUREMENT) {
            throw new ValidationException('El volumen debe ser un valor positivo');
        }
        if (!in_array($state, self::VALID_STATES, true)) {
            throw new ValidationException('El estado debe ser solido, liquido o gas');
        }
    }
}