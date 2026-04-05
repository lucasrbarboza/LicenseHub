<?php

namespace Config;

class Config
{
    public static function load(): void
    {
        $envFile = dirname(__DIR__) . '/.env';

        if (!file_exists($envFile)) {
            throw new \RuntimeException('Arquivo .env nao encontrado.');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new \RuntimeException('Falha ao ler o arquivo .env.');
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($value !== '' && $value[0] === '"' && str_ends_with($value, '"')) {
                $value = stripcslashes(substr($value, 1, -1));
            }

            $_ENV[$key] = $value;
        }
    }
}
