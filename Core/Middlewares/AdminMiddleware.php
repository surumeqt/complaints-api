<?php
namespace Core\Middlewares;

use Core\Http\Request;
use Exception;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $auth = $request->param('auth');

        if (!$auth) {
            throw new Exception('Unauthorized', 401);
        }

        if (($auth['role'] ?? null) !== 'admin') {
            throw new Exception("Forbidden: Admins only", 403);
        }

        return $next($request);
    }
}