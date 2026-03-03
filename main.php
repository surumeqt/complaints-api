<?php

if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $file = __DIR__ . $path;

    if (is_file($file)) {
        return false;
    }
}

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . 'Core/autoload.php';

use Core\Http\Request;
use Core\Http\Router;
use Core\Middlewares\CorsMiddleware;
use Core\Middlewares\ErrorMiddleware;

$request = new Request();
$router = new Router($request);

require __DIR__ . '/Routes/auth-api.php';
require __DIR__ . '/Routes/user-api.php';
require __DIR__ . '/Routes/complaint-api.php';

$errorMiddleware = new ErrorMiddleware();

$errorMiddleware->handle($request, function ($request) use ($router) {

    return (new CorsMiddleware())->handle($request, function ($request) use ($router) {

        return $router->resolve();

    });

});