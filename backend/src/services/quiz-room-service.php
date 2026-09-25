<?php

declare(strict_types=1);

final class QuizRoomService
{
    private const CODE_LENGTH = 6;
    private const MAX_CODE_ATTEMPTS = 100;
    private const CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    private const MIN_QUESTIONS = 1;
    private const MAX_QUESTIONS = 20;
    private const OPTIONS_PER_QUESTION = 4;
    private const MAX_TITLE_LENGTH = 120;
    private const MAX_QUESTION_TEXT_LENGTH = 255;
    private const MAX_OPTION_TEXT_LENGTH = 120;
    private const DEFAULT_POINTS = 10;
    private const MIN_POINTS = 1;
    private const MAX_POINTS = 100;
    private const STATUS_OPEN = 'abierta';

    public function __construct(
        private readonly QuizRoomRepository $roomRepository,
        private readonly QuizQuestionRepository $questionRepository,
        private readonly QuizAttemptRepository $attemptRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    public function createRoom(int $creatorId, string $title, array $questions): array
    {
        $this->assertQuestionList($questions);
        $room = new QuizRoom(null, $creatorId, trim($title), $this->generateUniqueCode(), self::STATUS_OPEN, null);
        $roomId = $this->roomRepository->create($room);
        $this->storeQuestions($roomId, $questions);

        return $this->buildRoomPayload($roomId);
    }

    public function listRooms(int $creatorId): array
    {
        $rooms = $this->roomRepository->findByCreator($creatorId);

        return array_map(
            static fn(QuizRoom $room): array => $room->toArray(),
            $rooms
        );
    }

    public function getRoom(int $roomId, int $creatorId): array
    {
        $room = $this->roomRepository->findById($roomId);
        if ($room === null || $room->creatorId !== $creatorId) {
            throw new NotFoundException('Sala no encontrada');
        }

        return $this->buildRoomPayload($roomId);
    }

    public function deleteRoom(int $roomId, int $creatorId): void
    {
        if (!$this->roomRepository->deleteByIdAndCreator($roomId, $creatorId)) {
            throw new NotFoundException('Sala no encontrada');
        }
    }

    public function joinRoom(int $userId, string $code): array
    {
        $room = $this->roomRepository->findByCode($code);
        if ($room === null || $room->status !== self::STATUS_OPEN) {
            throw new NotFoundException('Sala no encontrada');
        }

        $existingAttempt = $this->attemptRepository->findByRoomAndUser($room->id, $userId);
        if ($existingAttempt !== null) {
            throw new ValidationException('Ya realizaste esta sala');
        }

        $questions = $this->questionRepository->findByRoomId($room->id);
        $attemptId = $this->attemptRepository->create(
            new QuizAttempt(null, $room->id, $userId, 0, count($questions), null, null)
        );

        return [
            'attempt_id' => $attemptId,
            'room' => [
                'id' => $room->id,
                'title' => $room->title,
                'code' => $room->code,
            ],
            'questions' => $this->publicQuestions($room->id),
            'total_questions' => count($questions),
        ];
    }

    public function submitAnswers(int $userId, int $attemptId, array $answers): array
    {
        $attempt = $this->attemptRepository->findById($attemptId);
        if ($attempt === null || $attempt->userId !== $userId) {
            throw new NotFoundException('Intento no encontrado');
        }
        if ($attempt->finishedAt !== null) {
            throw new ValidationException('Ya enviaste tus respuestas a esta sala');
        }

        $questionsById = $this->indexQuestions($attempt->roomId);
        if (count($answers) !== count($questionsById)) {
            throw new ValidationException('Responde todas las preguntas');
        }

        $result = $this->scoreAnswers($questionsById, $answers);
        $this->attemptRepository->finish($attemptId, $result['score']);
        if ($result['score'] > 0) {
            $this->userRepository->addPoints($userId, $result['score']);
        }

        return [
            'attempt_id' => $attemptId,
            'score' => $result['score'],
            'correct_count' => $result['correct_count'],
            'total_questions' => count($questionsById),
            'points_awarded' => $result['score'],
            'details' => $result['details'],
        ];
    }

    private function publicQuestions(int $roomId): array
    {
        $questions = $this->questionRepository->findWithOptions($roomId);

        return array_map(function (array $question): array {
            return [
                'id' => $question['id'],
                'question_text' => $question['question_text'],
                'points_value' => $question['points_value'],
                'options' => $question['options'],
            ];
        }, $questions);
    }

    private function indexQuestions(int $roomId): array
    {
        $questions = $this->questionRepository->findByRoomId($roomId);
        $indexed = [];
        foreach ($questions as $question) {
            $indexed[$question->id] = $question;
        }

        return $indexed;
    }

    private function scoreAnswers(array $questionsById, array $answers): array
    {
        $score = 0;
        $correctCount = 0;
        $details = [];
        $seen = [];

        foreach ($answers as $answer) {
            $questionId = (int) ($answer['question_id'] ?? 0);
            $selected = (int) ($answer['option_index'] ?? 0);
            if (!isset($questionsById[$questionId]) || isset($seen[$questionId])) {
                throw new ValidationException('Respuesta no válida');
            }
            if ($selected < 1 || $selected > self::OPTIONS_PER_QUESTION) {
                throw new ValidationException('Opción inválida');
            }
            $seen[$questionId] = true;
            $question = $questionsById[$questionId];
            $isCorrect = $selected === $question->correctOption;
            if ($isCorrect) {
                $score += $question->pointsValue;
                $correctCount++;
            }
            $details[] = [
                'question_id' => $questionId,
                'correct_option' => $question->correctOption,
                'selected_option' => $selected,
                'is_correct' => $isCorrect,
            ];
        }

        return [
            'score' => $score,
            'correct_count' => $correctCount,
            'details' => $details,
        ];
    }

    private function buildRoomPayload(int $roomId): array
    {
        $room = $this->roomRepository->findById($roomId);

        return [
            'room' => $room->toArray(),
            'questions' => $this->questionRepository->findWithOptions($roomId),
        ];
    }

    private function storeQuestions(int $roomId, array $questions): void
    {
        foreach ($questions as $question) {
            $model = new QuizQuestion(
                null,
                $roomId,
                trim($question['question_text']),
                (int) $question['correct_option'],
                (int) ($question['points_value'] ?? self::DEFAULT_POINTS)
            );
            $this->questionRepository->create($model, $question['options']);
        }
    }

    private function assertQuestionList(array $questions): void
    {
        $count = count($questions);
        if ($count < self::MIN_QUESTIONS || $count > self::MAX_QUESTIONS) {
            throw new ValidationException('La sala debe tener entre 1 y 20 preguntas');
        }
        foreach ($questions as $question) {
            $this->assertQuestion($question);
        }
    }

    private function assertQuestion(array $question): void
    {
        $text = trim($question['question_text'] ?? '');
        if ($text === '' || strlen($text) > self::MAX_QUESTION_TEXT_LENGTH) {
            throw new ValidationException('Cada pregunta debe tener texto (máximo 255 caracteres)');
        }

        $options = $question['options'] ?? [];
        if (count($options) !== self::OPTIONS_PER_QUESTION) {
            throw new ValidationException('Cada pregunta debe tener 4 opciones');
        }
        foreach ($options as $option) {
            $optionText = trim((string) $option);
            if ($optionText === '' || strlen($optionText) > self::MAX_OPTION_TEXT_LENGTH) {
                throw new ValidationException('Cada opción debe tener texto (máximo 120 caracteres)');
            }
        }

        $correct = (int) ($question['correct_option'] ?? 0);
        if ($correct < 1 || $correct > self::OPTIONS_PER_QUESTION) {
            throw new ValidationException('La opción correcta debe estar entre 1 y 4');
        }

        $points = (int) ($question['points_value'] ?? self::DEFAULT_POINTS);
        if ($points < self::MIN_POINTS || $points > self::MAX_POINTS) {
            throw new ValidationException('Los puntos por pregunta deben estar entre 1 y 100');
        }
    }

    private function generateUniqueCode(): string
    {
        $alphabetLength = strlen(self::CODE_ALPHABET);
        for ($attempt = 0; $attempt < self::MAX_CODE_ATTEMPTS; $attempt++) {
            $code = '';
            for ($i = 0; $i < self::CODE_LENGTH; $i++) {
                $code .= self::CODE_ALPHABET[random_int(0, $alphabetLength - 1)];
            }
            if ($this->roomRepository->findByCode($code) === null) {
                return $code;
            }
        }

        throw new RuntimeException('No se pudo generar un código único');
    }
}