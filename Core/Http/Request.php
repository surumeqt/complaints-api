<?php

namespace Core\Http;

use Exception;

class Request
{
    public string $method;
    public string $uri;
    public array $query = [];
    public array $body = [];
    public array $headers = [];
    public array $params = [];

    public function __construct()
    {
        $this->method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->query   = $_GET ?? [];
        $this->headers = $this->parseHeaders();
        $this->uri     = $this->parseUri();
        $this->body    = $this->parseBody();
    }

    private function parseUri(): string
    {
        $path = $_GET['params'] ?? '/';
        return '/' . trim($path, '/');
    }

    private function parseHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    private function parseBody(): array
    {
        if (in_array($this->method, ['GET', 'OPTIONS'])) {
            return [];
        }

        $raw = file_get_contents('php://input');

        if (!$raw) {
            return $_POST ?? [];
        }

        $contentType = $this->headers['Content-Type'] ?? '';

        if (str_contains($contentType, 'application/json')) {

            $data = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(
                    'Invalid JSON payload: ' . json_last_error_msg(),
                    400
                );
            }

            return $data ?? [];
        }

        return $_POST ?? [];
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function getAuthId()
    {
        $auth = $this->param('auth');
        return $auth['id'] ?? null;
    }

    public function validate(array $rules, bool $partial = false): array
    {
        $data = [];
        $missing = [];
        $extra = array_diff(array_keys($this->body), array_keys($rules));

        foreach ($rules as $field => $rule) {

            $exists = array_key_exists($field, $this->body);
            $value = $this->body[$field] ?? null;

            // Determine if required
            $required = is_bool($rule) ? $rule : true;

            if (!$partial && $required && (!$exists || $value === '')) {
                $missing[] = $field;
                continue;
            }

            if ($exists) {

                // If rule is string → treat as enum validation
                if (is_string($rule)) {
                    $allowed = explode('|', $rule);

                    if (!in_array($value, $allowed, true)) {
                        throw new Exception(
                            "Invalid value for {$field}. Allowed values: " . implode(', ', $allowed),
                            422
                        );
                    }
                }

                $data[$field] = $value;
            }
        }

        if (!$partial && $missing) {
            throw new Exception(
                'Missing or empty fields: ' . implode(', ', $missing),
                422
            );
        }

        if ($extra) {
            throw new Exception(
                'Unrecognized fields sent: ' . implode(', ', $extra),
                400
            );
        }

        return $data;
    }
}