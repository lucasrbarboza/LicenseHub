<?php

namespace Config;

class Config
{
    /**
     * Carrega variáveis de ambiente do arquivo .env
     */
    public static function load(): void
    {
        $envFile = dirname(__DIR__) . '/.env';

        if (!file_exists($envFile)) {
            throw new \Exception('Arquivo .env não encontrado. Execute: cp .env.example .env');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            $_ENV[$key] = $value;
        }
    }

    /**
     * Obtém uma variável de configuração
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * Define uma variável de configuração
     */
    public static function set(string $key, mixed $value): void
    {
        $_ENV[$key] = $value;
    }

    /**
     * Verifica se está em ambiente de desenvolvimento
     */
    public static function isDevelopment(): bool
    {
        return self::get('APP_ENV') === 'development';
    }

    /**
     * Retorna se o debug está ativado
     */
    public static function isDebug(): bool
    {
        return self::get('APP_DEBUG') === 'true';
    }
}
