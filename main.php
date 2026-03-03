<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/core/autoload.php';

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