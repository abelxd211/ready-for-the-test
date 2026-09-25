<?php

declare(strict_types=1);

final class LabMixingService
{
    public function __construct(
        private readonly MixtureReactionRepository $reactionRepository,
        private readonly DiscoveredReactionRepository $discoveredRepository,
        private readonly ReagentRepository $reagentRepository,
        private readonly LabExperimentService $experimentService
    ) {
    }

    public function mix(int $userId, string $reagentAId, string $reagentBId): array
    {
        $this->assertPair($reagentAId, $reagentBId);
        $reaction = $this->reactionRepository->findByPair($reagentAId, $reagentBId);

        if ($reaction === null) {
            $this->experimentService->recordActivity($userId, 'mezcla', 'No se produjo reacción visible', null, 0);

            return [
                'reaction' => null,
                'effect' => 'ninguno',
                'is_new' => false,
                'points_awarded' => 0,
            ];
        }

        return $this->registerDiscovery($userId, $reaction);
    }

    private function assertPair(string $reagentAId, string $reagentBId): void
    {
        if ($reagentAId === '' || $reagentBId === '') {
            throw new ValidationException('Selecciona dos reactivos');
        }
        if ($reagentAId === $reagentBId) {
            throw new ValidationException('Selecciona dos reactivos distintos');
        }
        if (!$this->reagentRepository->existsById($reagentAId) || !$this->reagentRepository->existsById($reagentBId)) {
            throw new ValidationException('Reactivo no válido');
        }
    }

    private function registerDiscovery(int $userId, MixtureReaction $reaction): array
    {
        $isNew = !$this->discoveredRepository->has($userId, $reaction->id);
        $points = $isNew
            ? LabExperimentService::EXPERIMENT_BASE_POINTS + LabExperimentService::DISCOVERY_BONUS_POINTS
            : LabExperimentService::EXPERIMENT_BASE_POINTS;

        if ($isNew) {
            $this->discoveredRepository->create($userId, $reaction->id);
        }
        $this->experimentService->recordActivity($userId, 'mezcla', 'Descubriste: ' . $reaction->resultName, null, $points);

        return [
            'reaction' => $reaction->toArray(),
            'effect' => $reaction->effect,
            'is_new' => $isNew,
            'points_awarded' => $points,
        ];
    }
}