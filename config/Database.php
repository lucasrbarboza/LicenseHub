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
    /**
     * Obtém a conexão com o banco de dados (PDO ou fallback compatível)
     * Retorna um objeto com interface compatível (PDO ou MysqliPdoCompat)
     */
    public static function getConnection()
    {
        if (self::$connection === null) {
            self::$connection = self::createConnection();
        }
        return self::$connection;
    }

    /**
     * Cria uma nova conexão com o banco
     */
    private static function createConnection()
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? 3306;
        $database = $_ENV['DB_NAME'] ?? 'licensehub';
        $user = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        // Debug: registrar o estado atual antes de tentar conectar
        $debugDir = dirname(__DIR__) . '/logs';
        if (!is_dir($debugDir)) {
            @mkdir($debugDir, 0755, true);
        }
        $debugFile = $debugDir . '/db_debug.log';
        $debugMsg = sprintf(
            "[%s] DB debug start\nSAPI: %s\nphp_ini: %s\nextensions_has_pdo: %s\nextensions_has_pdo_mysql: %s\npdo_drivers: %s\nENV_DB: %s\nDSN: mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4\n\n",
            date('c'),
            php_sapi_name(),
            php_ini_loaded_file() ?: 'none',
            extension_loaded('pdo') ? 'yes' : 'no',
            extension_loaded('pdo_mysql') ? 'yes' : 'no',
            implode(',', PDO::getAvailableDrivers()),
            json_encode(['DB_HOST'=>$host,'DB_PORT'=>$port,'DB_NAME'=>$database,'DB_USER'=>$user], JSON_UNESCAPED_UNICODE),
            $host, $port, $database
        );
        @file_put_contents($debugFile, $debugMsg, FILE_APPEND | LOCK_EX);

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            
            $pdo = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return $pdo;
        } catch (PDOException $e) {
            // If PDO MySQL driver is not available, fallback to mysqli adapter
            $drivers = PDO::getAvailableDrivers();
            $driversList = implode(',', $drivers);
            if (!in_array('mysql', $drivers, true) || stripos($e->getMessage(), 'could not find driver') !== false) {
                // Attempt mysqli fallback
                try {
                    require_once dirname(__DIR__, 1) . '/app/Core/MysqliPdoCompat.php';
                    $adapter = new \App\Core\MysqliPdoCompat($host, (int)$port, $database, $user, $password);
                    return $adapter;
                } catch (\Throwable $ex) {
                    // Log fallback failure
                    $logDir = dirname(__DIR__) . '/logs';
                    if (!is_dir($logDir)) {
                        @mkdir($logDir, 0755, true);
                    }
                    $logFile = $logDir . '/db_error.log';
                    $msg = sprintf("[%s] Falha ao usar fallback mysqli: %s\n", date('c'), $ex->getMessage());
                    @file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
                    throw $e; // rethrow original PDO exception
                }
            }

            // Log detalhado para diagnóstico
            $logDir = dirname(__DIR__) . '/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . '/db_error.log';
            $drivers = implode(',', PDO::getAvailableDrivers());
            $envInfo = [
                'DB_HOST' => $_ENV['DB_HOST'] ?? null,
                'DB_PORT' => $_ENV['DB_PORT'] ?? null,
                'DB_NAME' => $_ENV['DB_NAME'] ?? null,
                'DB_USER' => $_ENV['DB_USER'] ?? null,
                'APP_ENV' => $_ENV['APP_ENV'] ?? null,
            ];
            $msg = sprintf(
                "[%s] Erro conectar DB: %s\nDSN: %s\nDrivers: %s\nEnv: %s\nTrace: %s\n\n",
                date('c'),
                $e->getMessage(),
                $dsn,
                $drivers,
                json_encode($envInfo, JSON_UNESCAPED_UNICODE),
                $e->getTraceAsString()
            );
            @file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);

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
