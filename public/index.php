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

// Habilita exibição de erros em desenvolvimento
if (Config::isDebug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Registra error handler customizado
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => Config::isDebug() ? $errstr : 'Erro interno do servidor',
        'file' => Config::isDebug() ? $errfile : null,
        'line' => Config::isDebug() ? $errline : null,
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

// Carrega rotas
try {
    $router = require_once dirname(__DIR__) . '/routes/api.php';
    $router->dispatch();
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => Config::isDebug() ? $e->getMessage() : 'Erro ao processar requisição',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
