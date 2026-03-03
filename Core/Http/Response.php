<?php

namespace Core\Http;

class Response
{
    public static function json($data, $code = 200) 
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
    public static function noContent() 
    {
        http_response_code(204);
        exit;
    }
    public static function error($message, $code = 500) 
    {
        self::json(['error' => $message], $code);
        exit;
    }
}