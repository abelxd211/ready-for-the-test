<?php

declare(strict_types=1);

final class AssistantController
{
    public function __construct(private readonly AssistantService $service)
    {
    }

    public function query(User $user): void
    {
        $requestBody = readJsonBody();
        $message = trim($requestBody['message'] ?? '');
        if ($message === '') {
            throw new ValidationException('Escribe tu consulta');
        }
        if (mb_strlen($message) > AssistantService::MAX_MESSAGE_LENGTH) {
            throw new ValidationException('Consulta demasiado larga');
        }

        $context = $this->normalizeContext($requestBody['context'] ?? null);
        $result = $this->service->answer($user, $context, $message);

        jsonResponse(200, true, $result, 'Consulta respondida');
    }

    public function help(User $user): void
    {
        $context = $this->normalizeContext($_GET['context'] ?? null);

        jsonResponse(200, true, $this->service->help($context), 'Temas de ayuda');
    }

    public function context(User $user): void
    {
        jsonResponse(200, true, $this->service->snapshot($user), 'Contexto del usuario');
    }

    private function normalizeContext(mixed $context): string
    {
        if ($context === null || trim((string) $context) === '') {
            return AssistantService::DEFAULT_CONTEXT;
        }
        $normalized = strtolower(trim((string) $context));
        if (!in_array($normalized, AssistantService::VALID_CONTEXTS, true)) {
            throw new ValidationException('Contexto no válido');
        }

        return $normalized;
    }
}