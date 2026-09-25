<?php

declare(strict_types=1);

final class QuizRoomRepository
{
    private const ROOM_COLUMNS = 'id, creator_id, title, code, status, created_at FROM quiz_rooms';

    public function __construct(private readonly PDO $database)
    {
    }

    public function findByCode(string $code): ?QuizRoom
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::ROOM_COLUMNS . ' WHERE code = :code LIMIT 1'
        );
        $statement->execute(['code' => $code]);

        $row = $statement->fetch();
        return $row === false ? null : QuizRoom::fromRow($row);
    }

    public function findById(int $roomId): ?QuizRoom
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::ROOM_COLUMNS . ' WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $roomId]);

        $row = $statement->fetch();
        return $row === false ? null : QuizRoom::fromRow($row);
    }

    public function findByCreator(int $creatorId): array
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::ROOM_COLUMNS . ' WHERE creator_id = :creator_id ORDER BY created_at DESC'
        );
        $statement->execute(['creator_id' => $creatorId]);

        return array_map(
            static fn(array $row): QuizRoom => QuizRoom::fromRow($row),
            $statement->fetchAll()
        );
    }

    public function create(QuizRoom $room): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO quiz_rooms (creator_id, title, code) VALUES (:creator_id, :title, :code)'
        );
        $statement->execute([
            'creator_id' => $room->creatorId,
            'title' => $room->title,
            'code' => $room->code,
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function deleteByIdAndCreator(int $roomId, int $creatorId): bool
    {
        $statement = $this->database->prepare(
            'DELETE FROM quiz_rooms WHERE id = :id AND creator_id = :creator_id'
        );
        $statement->execute([
            'id' => $roomId,
            'creator_id' => $creatorId,
        ]);

        return $statement->rowCount() > 0;
    }
}