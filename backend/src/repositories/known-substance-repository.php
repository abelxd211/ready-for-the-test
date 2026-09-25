<?php

declare(strict_types=1);

final class KnownSubstanceRepository
{
    private const SUBSTANCE_COLUMNS = 'id, name, description, state, transparency, conductivity, density, detail FROM known_substances';

    public function __construct(private readonly PDO $database)
    {
    }

    public function all(): array
    {
        $rows = $this->database->query(
            'SELECT ' . self::SUBSTANCE_COLUMNS . ' ORDER BY name'
        )->fetchAll();

        return array_map(
            static fn(array $row): KnownSubstance => KnownSubstance::fromRow($row),
            $rows
        );
    }

    public function findByProperties(string $state, string $transparency, string $conductivity): array
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::SUBSTANCE_COLUMNS . ' WHERE state = :state AND transparency = :transparency AND conductivity = :conductivity'
        );
        $statement->execute([
            'state' => $state,
            'transparency' => $transparency,
            'conductivity' => $conductivity,
        ]);

        return array_map(
            static fn(array $row): KnownSubstance => KnownSubstance::fromRow($row),
            $statement->fetchAll()
        );
    }
}