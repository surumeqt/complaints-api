<?php
namespace Core\Middlewares;

use Core\Http\Request;

class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        header('Access-Control-Allow-Origin: ' . (getenv('CORS_ORIGIN') ?? '*'));
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');

        if ($request->method === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        return $next($request);
    }
}