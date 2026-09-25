<?php

declare(strict_types=1);

final class LabExperimentService
{
    public const EXPERIMENT_BASE_POINTS = 2;
    public const DISCOVERY_BONUS_POINTS = 5;

    private const MAX_SUMMARY_LENGTH = 255;

    public function __construct(
        private readonly LabExperimentRepository $experimentRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    public function recordActivity(int $userId, string $type, string $summary, ?int $sampleId, int $points): LabExperiment
    {
        $limitedSummary = mb_substr($summary, 0, self::MAX_SUMMARY_LENGTH);
        $experiment = new LabExperiment(null, $userId, $sampleId, $type, $limitedSummary, $points, null);
        $experimentId = $this->experimentRepository->create($experiment);

        if ($points > 0) {
            $this->userRepository->addPoints($userId, $points);
        }

        return $this->experimentRepository->findById($experimentId);
    }

    public function listFor(int $userId): array
    {
        $experiments = $this->experimentRepository->findByUserId($userId);

        return array_map(
            static fn(LabExperiment $experiment): array => $experiment->toArray(),
            $experiments
        );
    }
}