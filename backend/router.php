<?php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

if (str_starts_with($path, '/api')) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    require __DIR__ . '/public/index.php';
    return;
}

serveFrontend($path);

function serveFrontend(string $path): void
{
    $root = realpath(__DIR__ . '/../frontend');
    $candidates = ($path === '/' || $path === '') ? ['/index.html'] : [$path];
    foreach ($candidates as $candidate) {
        $file = realpath($root . $candidate);
        if ($file !== false && str_starts_with($file, $root . DIRECTORY_SEPARATOR) && is_file($file)) {
            deliver($file);
            return;
        }
    }

    http_response_code(404);
    echo 'Recurso no encontrado';
}

function deliver(string $file): void
{
    header('Content-Type: ' . mimeFor($file));
    readfile($file);
}

function mimeFor(string $file): string
{
    $mimeTypes = [
        'html' => 'text/html; charset=utf-8',
        'css' => 'text/css; charset=utf-8',
        'js' => 'text/javascript; charset=utf-8',
        'json' => 'application/json',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'ico' => 'image/x-icon',
    ];
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    return $mimeTypes[$extension] ?? 'application/octet-stream';
}