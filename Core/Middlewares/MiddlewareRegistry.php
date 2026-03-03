<?php
namespace Core\Middlewares;
use Exception;
class MiddlewareRegistry
{
    protected static array $map = [
        'auth' => AuthMiddleware::class,
        'admin' => AdminMiddleware::class,
    ];

    public static function resolve(array $keys): array
    {
        $middlewares = [];

        foreach ($keys as $key) {
            if (!isset(self::$map[$key])) {
                throw new Exception("Middleware [$key] not registered");
            }
            $middlewares[] = new self::$map[$key]();
        }

        return $middlewares;
    }
}