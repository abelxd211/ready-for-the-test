<?php

declare(strict_types=1);

final class Token
{
    public static function encode(int $userId, string $role, int $ttlSeconds, string $secret): string
    {
        $issuedAt = time();
        $payload = json_encode([
            'user_id' => $userId,
            'role' => $role,
            'iat' => $issuedAt,
            'exp' => $issuedAt + $ttlSeconds,
        ], JSON_THROW_ON_ERROR);

        $encodedPayload = self::base64UrlEncode($payload);
        return $encodedPayload . '.' . hash_hmac('sha256', $encodedPayload, $secret);
    }

    public static function decode(string $token, string $secret): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        [$encodedPayload, $signature] = $parts;
        $expectedSignature = hash_hmac('sha256', $encodedPayload, $secret);
        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        $payload = json_decode(self::base64UrlDecode($encodedPayload), true);
        if (!is_array($payload) || (($payload['exp'] ?? 0) < time())) {
            return null;
        }

        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder > 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'), true) ?: '';
    }
}