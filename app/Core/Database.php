<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static array $config = [];
    private static ?PDO $pdo = null;

    public static function init(array $config): void
    {
        self::$config = $config;
    }

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }
        $host = self::$config['host'] ?? '127.0.0.1';
        $port = (int)(self::$config['port'] ?? 3306);
        $db = self::$config['name'] ?? 'ai';
        $charset = self::$config['charset'] ?? 'utf8mb4';
        $user = self::$config['user'] ?? 'root';
        $pass = self::$config['pass'] ?? '';
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
            exit;
        }
        return self::$pdo;
    }
}


