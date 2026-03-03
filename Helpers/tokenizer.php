<?php

function generateAndSetToken(array $payload)
{
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload['exp'] = time() + (60 * 60 * 24);
    $payload = json_encode($payload);

    $base64UrlHeader = base64UrlEncode($header);
    $base64UrlPayload = base64UrlEncode($payload);

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $_ENV['JWT_SECRET'], true);
    $base64UrlSignature = base64UrlEncode($signature);

    $token =  $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    return setcookie('token', $token, [
        'expires' => time() + (60 * 60 * 24),
        'path' => '/',
        'secure' => $_ENV['APP_ENV'] === 'prod',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}
function verify(string $token)
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    [$header, $payload, $signature] = $parts;

    $validSignature = base64UrlEncode(hash_hmac('sha256', $header . "." . $payload, $_ENV['JWT_SECRET'], true));

    if ($signature !== $validSignature) return false;

    $decodedPayload = json_decode(base64_decode($payload), true);
    if ($decodedPayload['exp'] < time()) return false;

    return $decodedPayload;
}
function clearTokenCookie()
{
    setcookie("token", "", time() - 3600, "/");
}
function base64UrlEncode($data): string 
{
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}