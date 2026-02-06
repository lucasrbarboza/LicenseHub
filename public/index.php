<?php

// =====================================================
// LicenseHub API - Entry Point
// =====================================================

// Headers padrão
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Trata preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Autoloader do Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Carrega configurações
use Config\Config;

Config::load();

// Inicia buffer de saída para evitar qualquer HTML acidental sendo enviado
ob_start();

// Sempre suprimir saída HTML de erros do PHP — retornamos JSON em todos os casos
ini_set('display_errors', '0');

if (Config::isDebug()) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

// Registra error handler customizado (erros não fatais)
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    $resp = [
        'success' => false,
        'message' => Config::isDebug() ? $errstr : 'Erro interno do servidor',
    ];
    if (Config::isDebug()) {
        $resp['file'] = $errfile;
        $resp['line'] = $errline;
    }

    if (ob_get_length() !== false) {
        ob_end_clean();
    }

    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    exit;
});

// Registra handler para exceções não capturadas
set_exception_handler(function (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');

    $resp = [
        'success' => false,
        'message' => Config::isDebug() ? $e->getMessage() : 'Erro não tratado no servidor',
    ];

    if (Config::isDebug()) {
        $resp['file'] = $e->getFile();
        $resp['line'] = $e->getLine();
        $resp['trace'] = $e->getTraceAsString();
    }

    if (ob_get_length() !== false) {
        ob_end_clean();
    }

    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    exit;
});

// Handler de shutdown para capturar erros fatais
register_shutdown_function(function () {
    $error = error_get_last();
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

    if ($error && in_array($error['type'], $fatalTypes, true)) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');

        $message = Config::isDebug() ? $error['message'] : 'Erro fatal no servidor';
        $resp = [
            'success' => false,
            'message' => $message,
        ];

        if (Config::isDebug()) {
            $resp['file'] = $error['file'];
            $resp['line'] = $error['line'];
        }

        if (ob_get_length() !== false) {
            ob_end_clean();
        }

        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    }
});

// Carrega rotas
try {
    $router = require_once dirname(__DIR__) . '/routes/api.php';
    $router->dispatch();
} catch (\Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    if (ob_get_length() !== false) {
        ob_end_clean();
    }
    echo json_encode([
        'success' => false,
        'message' => Config::isDebug() ? $e->getMessage() : 'Erro ao processar requisição',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
