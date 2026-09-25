<?php

declare(strict_types=1);

final class QuizQuestionRepository
{
    private const QUESTION_COLUMNS = 'id, room_id, question_text, correct_option, points_value FROM quiz_questions';

    public function __construct(private readonly PDO $database)
    {
    }

    public function create(QuizQuestion $question, array $options): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO quiz_questions (room_id, question_text, correct_option, points_value) VALUES (:room_id, :question_text, :correct_option, :points_value)'
        );
        $statement->execute([
            'room_id' => $question->roomId,
            'question_text' => $question->questionText,
            'correct_option' => $question->correctOption,
            'points_value' => $question->pointsValue,
        ]);
        $questionId = (int) $this->database->lastInsertId();

        $optionStatement = $this->database->prepare(
            'INSERT INTO quiz_question_options (question_id, option_position, option_text) VALUES (:question_id, :option_position, :option_text)'
        );
        foreach ($options as $position => $optionText) {
            $optionStatement->execute([
                'question_id' => $questionId,
                'option_position' => $position + 1,
                'option_text' => trim((string) $optionText),
            ]);
        }

        return $questionId;
    }

    public function findByRoomId(int $roomId): array
    {
        $statement = $this->database->prepare(
            'SELECT ' . self::QUESTION_COLUMNS . ' WHERE room_id = :room_id ORDER BY id'
        );
        $statement->execute(['room_id' => $roomId]);

        return array_map(
            static fn(array $row): QuizQuestion => QuizQuestion::fromRow($row),
            $statement->fetchAll()
        );
    }

    public function findWithOptions(int $roomId): array
    {
        $questions = $this->findByRoomId($roomId);
        $questionIds = array_map(
            static fn(QuizQuestion $question): int => $question->id,
            $questions
        );

        if ($questionIds === []) {
            return [];
        }

        $optionsByQuestion = $this->findOptionsForQuestions($questionIds);
        $result = [];
        foreach ($questions as $question) {
            $options = $optionsByQuestion[$question->id] ?? [];
            $result[] = [
                'id' => $question->id,
                'question_text' => $question->questionText,
                'correct_option' => $question->correctOption,
                'points_value' => $question->pointsValue,
                'options' => array_map(
                    static fn(QuizQuestionOption $option): array => [
                        'position' => $option->position,
                        'text' => $option->optionText,
                    ],
                    $options
                ),
            ];
        }

        return $result;
    }

    private function findOptionsForQuestions(array $questionIds): array
    {
        $placeholders = implode(',', array_fill(0, count($questionIds), '?'));
        $statement = $this->database->prepare(
            'SELECT id, question_id, option_position, option_text FROM quiz_question_options WHERE question_id IN (' . $placeholders . ') ORDER BY question_id, option_position'
        );
        $statement->execute($questionIds);

        $grouped = [];
        foreach ($statement->fetchAll() as $row) {
            $grouped[(int) $row['question_id']][] = QuizQuestionOption::fromRow($row);
        }

        return $grouped;
    }
}