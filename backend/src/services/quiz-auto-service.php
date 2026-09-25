<?php

declare(strict_types=1);

final class QuizAutoService
{
    private const MAX_AUTO_QUESTIONS = 10;
    private const OPTIONS_PER_QUESTION = 4;

    public function __construct(
        private readonly QuizRoomService $roomService
    ) {
    }

    public function topics(): array
    {
        return array_map(function (array $entry): array {
            return [
                'id' => $entry['id'],
                'label' => $entry['label'],
                'icon' => $entry['icon'],
                'question_count' => count($entry['questions']),
            ];
        }, $this->allTopics());
    }

    public function createAutoRoom(int $creatorId, string $title, string $topic, int $questionCount, int $pointsValue): array
    {
        $template = $this->findTopic($topic);
        if ($template === null) {
            throw new ValidationException('Tema no disponible');
        }
        if ($questionCount < 1 || $questionCount > self::MAX_AUTO_QUESTIONS) {
            throw new ValidationException('Elige entre 1 y 10 preguntas');
        }
        if ($questionCount > count($template['questions'])) {
            throw new ValidationException("El tema solo tiene " . count($template['questions']) . ' preguntas');
        }
        if ($pointsValue < 1 || $pointsValue > 100) {
            throw new ValidationException('Los puntos por pregunta deben estar entre 1 y 100');
        }

        $roomTitle = trim($title) !== ''
            ? trim($title)
            : "Sala IA: {$template['label']}";

        $pool = $template['questions'];
        shuffle($pool);
        $selected = array_slice($pool, 0, $questionCount);

        $questions = [];
        foreach ($selected as $question) {
            $questions[] = $this->formattedQuestion($question, $pointsValue);
        }

        return $this->roomService->createRoom($creatorId, $roomTitle, $questions);
    }

    private function formattedQuestion(array $question, int $pointsValue): array
    {
        $indices = [0, 1, 2, 3];
        shuffle($indices);
        $options = [];
        $correctOption = 1;

        foreach ($indices as $position => $sourceIndex) {
            $options[] = $question['options'][$sourceIndex];
            if ($sourceIndex === $question['correct']) {
                $correctOption = $position + 1;
            }
        }

        return [
            'question_text' => $question['q'],
            'options' => $options,
            'correct_option' => $correctOption,
            'points_value' => $pointsValue,
        ];
    }

    private function allTopics(): array
    {
        $topics = require __DIR__ . '/../config/quiz-templates-math.php';
        $labTopics = require __DIR__ . '/../config/quiz-templates-lab.php';

        foreach ($labTopics as $id => $entry) {
            $topics[$id] = $entry;
        }

        $result = [];
        foreach ($topics as $id => $entry) {
            $result[] = [
                'id' => $id,
                'label' => $entry['label'],
                'icon' => $entry['icon'],
                'questions' => $entry['questions'],
            ];
        }

        return $result;
    }

    private function findTopic(string $id): ?array
    {
        foreach ($this->allTopics() as $topic) {
            if ($topic['id'] === $id) {
                return $topic;
            }
        }

        return null;
    }
}