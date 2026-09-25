<?php

declare(strict_types=1);

final class AssistantService
{
    public const MAX_MESSAGE_LENGTH = 500;
    public const DEFAULT_CONTEXT = 'general';
    public const VALID_CONTEXTS = [
        'general',
        'perfil',
        'calculadora',
        'laboratorio',
        'catalogo',
        'mezclas',
        'identificacion',
        'salas',
    ];

    private const ACCENT_MAP = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'ü' => 'u',
        'ñ' => 'n',
    ];

    private static array $knowledgeCache = [];

    public function __construct(
        private readonly CalculatorOperationRepository $calculatorOperationRepository,
        private readonly LabSampleRepository $labSampleRepository,
        private readonly LabExperimentRepository $labExperimentRepository,
        private readonly DiscoveredReactionRepository $discoveredReactionRepository,
        private readonly QuizAttemptRepository $quizAttemptRepository
    ) {
    }

    public function answer(User $user, string $context, string $message): array
    {
        $normalizedMessage = self::normalizeText($message);
        $topics = $this->availableTopics($context);
        $snapshot = $this->buildSnapshot($user);
        $matchedTopic = $this->bestTopic($topics, $normalizedMessage, $context);

        if ($matchedTopic === null) {
            return [
                'response' => $this->interpolate($this->buildFallback($topics), $user, $snapshot),
                'topic' => null,
                'suggestions' => $this->topicSuggestions($topics),
                'user_context' => $snapshot,
            ];
        }

        return [
            'response' => $this->interpolate($matchedTopic['answer'], $user, $snapshot),
            'topic' => $matchedTopic['id'],
            'suggestions' => $matchedTopic['suggestions'],
            'user_context' => $snapshot,
        ];
    }

    public function help(string $context): array
    {
        $topics = $this->availableTopics($context);

        return array_map(static function (array $topic): array {
            return [
                'id' => $topic['id'],
                'title' => $topic['title'],
                'contexts' => $topic['contexts'],
                'suggestions' => $topic['suggestions'],
            ];
        }, $topics);
    }

    public function snapshot(User $user): array
    {
        return $this->buildSnapshot($user);
    }

    private function availableTopics(string $context): array
    {
        return array_values(array_filter(
            $this->knowledge(),
            static function (array $topic) use ($context): bool {
                return $context === self::DEFAULT_CONTEXT
                    || in_array($context, $topic['contexts'], true);
            }
        ));
    }

    private function bestTopic(array $topics, string $normalizedMessage, string $context): ?array
    {
        $bestTopic = null;
        $bestScore = 0;

        foreach ($topics as $topic) {
            $score = 0;
            foreach ($topic['keywords'] as $keyword) {
                if (str_contains($normalizedMessage, $keyword)) {
                    $score++;
                }
            }
            if ($score === 0) {
                continue;
            }
            if ($score > $bestScore) {
                $bestTopic = $topic;
                $bestScore = $score;
                continue;
            }
            if ($score === $bestScore && $bestTopic !== null) {
                $bestHasContext = in_array($context, $bestTopic['contexts'], true);
                $currentHasContext = in_array($context, $topic['contexts'], true);
                if (!$bestHasContext && $currentHasContext) {
                    $bestTopic = $topic;
                }
            }
        }

        return $bestTopic;
    }

    private function knowledge(): array
    {
        if (self::$knowledgeCache === []) {
            self::$knowledgeCache = require __DIR__ . '/../config/assistant-knowledge.php';
        }

        return self::$knowledgeCache;
    }

    private function buildSnapshot(User $user): array
    {
        $firstName = explode(' ', trim($user->fullName))[0] ?: $user->fullName;

        return [
            'name' => $firstName,
            'role' => $user->role,
            'points' => $user->points,
            'calculations' => $this->calculatorOperationRepository->countFor($user->id),
            'samples' => $this->labSampleRepository->countFor($user->id),
            'experiments' => $this->labExperimentRepository->countFor($user->id),
            'reactions' => $this->discoveredReactionRepository->countFor($user->id),
            'quizzes' => $this->quizAttemptRepository->countFinishedFor($user->id),
        ];
    }

    private function interpolate(string $answer, User $user, array $snapshot): string
    {
        $replacements = [
            '{name}' => $snapshot['name'],
            '{points}' => (string) $snapshot['points'],
            '{calculations}' => (string) $snapshot['calculations'],
            '{samples}' => (string) $snapshot['samples'],
            '{experiments}' => (string) $snapshot['experiments'],
            '{reactions}' => (string) $snapshot['reactions'],
            '{quizzes}' => (string) $snapshot['quizzes'],
            '{base}' => (string) LabExperimentService::EXPERIMENT_BASE_POINTS,
            '{bonus}' => (string) LabExperimentService::DISCOVERY_BONUS_POINTS,
        ];

        return strtr($answer, $replacements);
    }

    private function buildFallback(array $topics): string
    {
        $names = array_map(
            static fn(array $topic): string => $topic['title'],
            array_slice($topics, 0, 4)
        );

        return 'No encontré una respuesta exacta para eso, {name}. Puedes preguntarme por: ' . implode(', ', $names)
            . ', o escribe con más detalle lo que necesitas.';
    }

    private function topicSuggestions(array $topics): array
    {
        $suggestions = [];
        foreach ($topics as $topic) {
            foreach ($topic['suggestions'] as $suggestion) {
                $suggestions[$suggestion] = true;
            }
        }

        return array_keys(array_slice($suggestions, 0, 5, true));
    }

    private static function normalizeText(string $text): string
    {
        $lowerText = mb_strtolower(trim($text));

        return strtr($lowerText, self::ACCENT_MAP);
    }
}