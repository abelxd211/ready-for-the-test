<?php

declare(strict_types=1);

final class LabCatalogService
{
    public function __construct(
        private readonly KnownSubstanceRepository $substanceRepository,
        private readonly ReagentRepository $reagentRepository,
        private readonly MixtureReactionRepository $reactionRepository
    ) {
    }

    public function knownSubstances(): array
    {
        $substances = $this->substanceRepository->all();

        return array_map(
            static fn(KnownSubstance $substance): array => $substance->toArray(),
            $substances
        );
    }

    public function reagents(): array
    {
        $reagents = $this->reagentRepository->all();

        return array_map(
            static fn(Reagent $reagent): array => $reagent->toArray(),
            $reagents
        );
    }

    public function reactions(): array
    {
        $reactions = $this->reactionRepository->all();

        return array_map(
            static fn(MixtureReaction $reaction): array => $reaction->toArray(),
            $reactions
        );
    }
}