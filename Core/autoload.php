<?php

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // skip comments
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
}

foreach (glob(__DIR__ . '/../helpers/*.php') as $helper) {
    require_once $helper; // load all helper functions
}

spl_autoload_register(function (string $class) {

    $baseDir = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR;

    // Normalize class name
    $class = ltrim($class, '\\');

    // Convert namespace to path
    $file = $baseDir
        . str_replace('\\', DIRECTORY_SEPARATOR, $class)
        . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});