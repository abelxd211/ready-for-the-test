<?php

declare(strict_types=1);

function jsonResponse(int $statusCode, bool $success, mixed $data, string $message): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
    ]);
}