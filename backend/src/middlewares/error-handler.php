<?php

declare(strict_types=1);

function handleApiError(Throwable $error): void
{
    $statusCode = isClientErrorCode($error->getCode()) ? $error->getCode() : 500;
    $message = isClientErrorCode($error->getCode())
        ? $error->getMessage()
        : 'Ocurrió un error interno en el servidor';

    jsonResponse($statusCode, false, null, $message);
    error_log($error->getMessage());
}

function isClientErrorCode(int $code): bool
{
    return $code >= 400 && $code <= 499;
}