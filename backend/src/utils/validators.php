<?php

declare(strict_types=1);

const MIN_PASSWORD_LENGTH = 8;
const MAX_FULL_NAME_LENGTH = 120;

function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidPassword(string $password): bool
{
    return strlen($password) >= MIN_PASSWORD_LENGTH;
}

function isValidFullName(string $fullName): bool
{
    $trimmedName = trim($fullName);
    return $trimmedName !== '' && strlen($trimmedName) <= MAX_FULL_NAME_LENGTH;
}

function isValidRole(string $role): bool
{
    return in_array($role, [User::ROLE_STUDENT, User::ROLE_TEACHER], true);
}