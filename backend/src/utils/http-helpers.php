<?php

declare(strict_types=1);

function readJsonBody(): array
{
    $rawBody = file_get_contents('php://input');
    $decodedBody = json_decode($rawBody, true);

    return is_array($decodedBody) ? $decodedBody : [];
}