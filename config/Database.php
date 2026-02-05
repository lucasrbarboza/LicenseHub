<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    /**
     * Obtém a conexão com o banco de dados
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = self::createConnection();
        }
        return self::$connection;
    }

    /**
     * Cria uma nova conexão com o banco
     */
    private static function createConnection(): PDO
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? 3306;
        $database = $_ENV['DB_NAME'] ?? 'licensehub';
        $user = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Fecha a conexão
     */
    public static function closeConnection(): void
    {
        self::$connection = null;
    }
}
