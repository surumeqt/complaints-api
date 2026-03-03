<?php
namespace Core\Middlewares;

use Core\Http\Request;
use Exception;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $token = $_COOKIE['token'] ?? null;
        if (!$token) {
            throw new Exception(
                'Unauthorized: No token provided',
                401
            );
        }

        $payload = verify($token);
        if (!$payload) {
            throw new Exception(
                'Unauthorized: Invalid or expired token',
                401
            );
        }

        $request->setParams(array_merge($request->params, ['auth' => $payload]));

        return $next($request);
    }
}