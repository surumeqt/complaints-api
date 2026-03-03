<?php

namespace Core\Config;

use PDO;
use PDOException;
use Exception;

class DatabaseConfig
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {

            try {

                $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
                $name = $_ENV['DB_NAME'] ?? '';
                $user = $_ENV['DB_USER'] ?? '';
                $pass = $_ENV['DB_PASS'] ?? '';

                $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);

            } catch (PDOException $e) {
                throw new Exception(
                    "Database connection failed.",
                    500
                );
            }
        }

        return self::$instance;
    }
}