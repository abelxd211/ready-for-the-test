<?php

declare(strict_types=1);

final class ValidationException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }
}

final class UnauthorizedException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 401);
    }
}

final class NotFoundException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 404);
    }
}

final class ForbiddenException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 403);
    }
}