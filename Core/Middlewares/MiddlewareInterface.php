<?php
namespace Core\Middlewares;

use Core\Http\Request;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next);
}