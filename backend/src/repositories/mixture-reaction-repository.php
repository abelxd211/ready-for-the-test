<?php

declare(strict_types=1);

final class MixtureReactionRepository
{
    private const REACTION_COLUMNS = 'id, reagent_a_id, reagent_b_id, result_name, description, effect, result_color_hex FROM mixture_reactions';

    public function __construct(private readonly PDO $database)
    {
    }

    public function all(): array
    {
        $rows = $this->database->query(
            'SELECT ' . self::REACTION_COLUMNS . ' ORDER BY id'
        )->fetchAll();

        return array_map(
            static fn(array $row): MixtureReaction => MixtureReaction::fromRow($row),
            $rows
        );
    }

    public function findByPair(string $reagentAId, string $reagentBId): ?MixtureReaction
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::REACTION_COLUMNS . ' WHERE (reagent_a_id = :a AND reagent_b_id = :b) OR (reagent_a_id = :b AND reagent_b_id = :a) LIMIT 1'
        );
        $statement->execute([
            'a' => $reagentAId,
            'b' => $reagentBId,
        ]);

        $row = $statement->fetch();
        return $row === false ? null : MixtureReaction::fromRow($row);
    }
}