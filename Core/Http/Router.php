<?php

namespace Core\Http;

use Core\Middlewares\MiddlewareRegistry;
use Exception;

class Router
{
    private array $routes = [];
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get($uri, $action, array $middlewares = [])
    {
        $this->addRoute('GET', $uri, $action, $middlewares);
    }

    public function post($uri, $action, array $middlewares = [])
    {
        $this->addRoute('POST', $uri, $action, $middlewares);
    }

    public function put($uri, $action, array $middlewares = [])
    {
        $this->addRoute('PUT', $uri, $action, $middlewares);
    }

    public function delete($uri, $action, array $middlewares = [])
    {
        $this->addRoute('DELETE', $uri, $action, $middlewares);
    }

    private function addRoute($method, $uri, $action, array $middlewares)
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $uri, $paramMatches);
        $paramNames = $paramMatches[1] ?? [];

        $pattern = preg_replace(
            '/\{[a-zA-Z0-9_]+\}/',
            '([a-zA-Z0-9_]+)',
            trim($uri, '/')
        );

        $this->routes[$method]['#^/' . $pattern . '$#'] = [
            'action' => $action,
            'middlewares' => $middlewares,
            'paramNames' => $paramNames
        ];
    }

    public function resolve()
    {
        $method = $this->request->method;
        $uri = $this->request->uri;

        foreach ($this->routes[$method] ?? [] as $pattern => $route) {

            if (preg_match($pattern, $uri, $matches)) {

                array_shift($matches);

                $params = [];

                foreach ($route['paramNames'] as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }

                $this->request->setParams($params);

                $controllerExecution = function ($request) use ($route) {
                    [$controllerClass, $methodName] = $route['action'];
                    $controller = new $controllerClass();
                    return $controller->$methodName($request);
                };

                $middlewares = MiddlewareRegistry::resolve($route['middlewares']);

                return $this->runMiddlewareStack($middlewares, $controllerExecution);
            }
        }

        throw new Exception('Route not found', 404);
    }

    private function runMiddlewareStack(array $middlewares, callable $controller)
    {
        $next = $controller;

        foreach (array_reverse($middlewares) as $middleware) {
            $next = function ($request) use ($middleware, $next) {
                return $middleware->handle($request, $next);
            };
        }

        return $next($this->request);
    }
}