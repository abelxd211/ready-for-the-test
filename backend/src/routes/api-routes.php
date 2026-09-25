<?php

declare(strict_types=1);

function currentRoute(): string
{
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = rtrim(dirname($scriptName), '/');

    if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
        $requestPath = substr($requestPath, strlen($basePath));
    }

    return '/' . ltrim($requestPath, '/');
}

function matchRoute(string $pattern, string $route, array &$params): bool
{
    $patternSegments = explode('/', trim($pattern, '/'));
    $routeSegments = explode('/', trim($route, '/'));

    if (count($patternSegments) !== count($routeSegments)) {
        return false;
    }

    $params = [];
    foreach ($patternSegments as $index => $segment) {
        if (str_starts_with($segment, '{') && str_ends_with($segment, '}')) {
            $params[trim($segment, '{}')] = $routeSegments[$index];
            continue;
        }
        if ($segment !== $routeSegments[$index]) {
            return false;
        }
    }

    return true;
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$route = currentRoute();
$routeParams = [];

if ($requestMethod === 'POST' && $route === '/api/auth/register') {
    $authController->register();
    return;
}

if ($requestMethod === 'POST' && $route === '/api/auth/login') {
    $authController->login();
    return;
}

if ($requestMethod === 'GET' && $route === '/api/me') {
    $currentUser = $authMiddleware->authenticate();
    $authController->me($currentUser);
    return;
}

if ($requestMethod === 'GET' && $route === '/api/calculator-operations') {
    $currentUser = $authMiddleware->authenticate();
    $calculatorOperationController->list($currentUser);
    return;
}

if ($requestMethod === 'POST' && $route === '/api/calculator-operations') {
    $currentUser = $authMiddleware->authenticate();
    $calculatorOperationController->create($currentUser);
    return;
}

if ($requestMethod === 'POST' && $route === '/api/lab-samples') {
    $currentUser = $authMiddleware->authenticate();
    $labSampleController->create($currentUser);
    return;
}

if ($requestMethod === 'GET' && $route === '/api/lab-samples') {
    $currentUser = $authMiddleware->authenticate();
    $labSampleController->list($currentUser);
    return;
}

if ($requestMethod === 'DELETE' && matchRoute('/api/lab-samples/{id}', $route, $routeParams)) {
    $currentUser = $authMiddleware->authenticate();
    $labSampleController->delete($currentUser, (int) $routeParams['id']);
    return;
}

if ($requestMethod === 'GET' && $route === '/api/known-substances') {
    $authMiddleware->authenticate();
    $labCatalogController->listSubstances();
    return;
}

if ($requestMethod === 'GET' && $route === '/api/reagents') {
    $authMiddleware->authenticate();
    $labCatalogController->listReagents();
    return;
}

if ($requestMethod === 'GET' && $route === '/api/mixture-reactions') {
    $authMiddleware->authenticate();
    $labCatalogController->listReactions();
    return;
}

if ($requestMethod === 'POST' && $route === '/api/mixing') {
    $currentUser = $authMiddleware->authenticate();
    $labMixingController->mix($currentUser);
    return;
}

if ($requestMethod === 'POST' && $route === '/api/identify') {
    $currentUser = $authMiddleware->authenticate();
    $labIdentificationController->identify($currentUser);
    return;
}

if ($requestMethod === 'GET' && $route === '/api/lab-experiments') {
    $currentUser = $authMiddleware->authenticate();
    $labExperimentController->list($currentUser);
    return;
}

if ($requestMethod === 'POST' && $route === '/api/quiz-rooms') {
    $currentUser = $authMiddleware->authenticate();
    $quizRoomController->create($currentUser);
    return;
}

if ($requestMethod === 'GET' && $route === '/api/quiz-rooms') {
    $currentUser = $authMiddleware->authenticate();
    $quizRoomController->list($currentUser);
    return;
}

if ($requestMethod === 'GET' && $route === '/api/quiz-rooms/auto/topics') {
    $currentUser = $authMiddleware->authenticate();
    $quizRoomController->autoTopics($currentUser);
    return;
}

if ($requestMethod === 'POST' && $route === '/api/quiz-rooms/auto') {
    $currentUser = $authMiddleware->authenticate();
    $quizRoomController->autoCreate($currentUser);
    return;
}

if ($requestMethod === 'POST' && $route === '/api/quiz-rooms/join') {
    $currentUser = $authMiddleware->authenticate();
    $quizRoomController->join($currentUser);
    return;
}

if ($requestMethod === 'GET' && matchRoute('/api/quiz-rooms/{id}', $route, $routeParams)) {
    $currentUser = $authMiddleware->authenticate();
    $quizRoomController->get($currentUser, (int) $routeParams['id']);
    return;
}

if ($requestMethod === 'DELETE' && matchRoute('/api/quiz-rooms/{id}', $route, $routeParams)) {
    $currentUser = $authMiddleware->authenticate();
    $quizRoomController->delete($currentUser, (int) $routeParams['id']);
    return;
}

if ($requestMethod === 'POST' && $route === '/api/quiz-answers') {
    $currentUser = $authMiddleware->authenticate();
    $quizRoomController->submit($currentUser);
    return;
}

if ($requestMethod === 'POST' && $route === '/api/assistant/query') {
    $currentUser = $authMiddleware->authenticate();
    $assistantController->query($currentUser);
    return;
}

if ($requestMethod === 'GET' && $route === '/api/assistant/help') {
    $currentUser = $authMiddleware->authenticate();
    $assistantController->help($currentUser);
    return;
}

if ($requestMethod === 'GET' && $route === '/api/assistant/context') {
    $currentUser = $authMiddleware->authenticate();
    $assistantController->context($currentUser);
    return;
}

jsonResponse(404, false, null, 'Ruta no encontrada');