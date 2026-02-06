<?php

namespace App\Core;

use Config\Config;

class Auth
{
    /**
     * Retorna se a autenticação é necessária para as requisições.
     * - Em `production` é obrigatória
     * - Em `development` respeita `API_AUTH_ENABLED` (padrão: false)
     */
    public static function isRequired(): bool
    {
        $env = Config::get('APP_ENV', 'development');

        if ($env === 'production') {
            return true;
        }

        return Config::get('API_AUTH_ENABLED') === 'true';
    }

    /**
     * Verifica a autenticação da requisição e interrompe com JSON em caso de falha.
     * Aceita os seguintes métodos de envio do token:
     *  - Header: Authorization: Bearer <token>
     *  - Header: X-API-KEY: <token>
     *  - Query string: ?api_key=<token>
     */
    public static function check(): void
    {
        if (!self::isRequired()) {
            return;
        }

        $expected = (string) Config::get('API_AUTH_TOKEN', '');
        if ($expected === '') {
            Response::error('Autenticação da API não configurada', 500);
        }

        // Obtém possíveis fontes do token
        $present = null;

        // HTTP Authorization (Bearer)
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $present = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $present = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        } elseif (function_exists('getallheaders')) {
            $h = getallheaders();
            foreach (['Authorization','authorization','AUTHORIZATION'] as $key) {
                if (!empty($h[$key])) {
                    $present = trim($h[$key]);
                    break;
                }
            }
        }

        if ($present !== null) {
            if (str_starts_with($present, 'Bearer ')) {
                $present = substr($present, 7);
            } elseif (str_starts_with($present, 'Token ')) {
                $present = substr($present, 6);
            }
        }

        // X-API-KEY header
        if (($present === null || $present === '') && !empty($_SERVER['HTTP_X_API_KEY'])) {
            $present = trim($_SERVER['HTTP_X_API_KEY']);
        }

        // Query string
        if (($present === null || $present === '') && !empty($_GET['api_key'])) {
            $present = trim((string) $_GET['api_key']);
        }

        if ($present === null || $present === '') {
            Response::error('Unauthorized', 401);
        }

        if (!hash_equals($expected, $present)) {
            Response::error('Unauthorized', 401);
        }
    }
}
