<?php

namespace Core\Middlewares;

use Core\Http\Request;
use Core\Http\Response;
use Throwable;

class ErrorMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        try {
            set_error_handler(function ($severity, $message, $file, $line) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            });

            return $next($request);

        } catch (Throwable $e) {

            if ($_ENV['APP_ENV'] == 'dev') $this->log($e);

            $statusCode = $e->getCode();

            if ($statusCode < 100 || $statusCode >= 600) {
                $statusCode = 500;
            }
            
            Response::json([
                'message' => $e->getMessage(),
                'file' => $_ENV['APP_ENV'] === 'prod' ? null : $e->getFile(),
                'line' => $_ENV['APP_ENV'] === 'prod' ? null : $e->getLine()
            ], $statusCode);
        }
    }

    private function log(Throwable $e): void
    {
        $logDir = __DIR__ . '/../../storage';
        $logFile = $logDir . '/error.log';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $message =
            "[" . date('Y-m-d H:i:s') . "] " .
            $e->getMessage() . " in " .
            $e->getFile() . ":" .
            $e->getLine() . PHP_EOL;

        error_log($message, 3, $logFile);
    }
}