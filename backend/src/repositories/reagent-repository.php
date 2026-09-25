<?php

declare(strict_types=1);

final class ReagentRepository
{
    private const REAGENT_COLUMNS = 'id, name, color_hex, icon, description FROM reagents';

    public function __construct(private readonly PDO $database)
    {
    }

    public function all(): array
    {
        $rows = $this->database->query(
            'SELECT ' . self::REAGENT_COLUMNS . ' ORDER BY name'
        )->fetchAll();

        return array_map(
            static fn(array $row): Reagent => Reagent::fromRow($row),
            $rows
        );
    }

    public function existsById(string $reagentId): bool
    {
        $statement = $this->database->prepare(
            'SELECT 1 FROM reagents WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $reagentId]);

        return $statement->fetch() !== false;
    }
}