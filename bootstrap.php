<?php
declare(strict_types=1);

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';

use Config\Config;
use Config\Database;

Config::load();

function db(): PDO
{
    return Database::getConnection();
}

function app_resources(): array
{
    static $resources = null;

    if ($resources === null) {
        $resources = require __DIR__ . '/config/resources.php';
    }

    return $resources;
}

function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_data(): array
{
    static $data = null;

    if ($data !== null) {
        return $data;
    }

    $data = $_POST;
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

    if ($method === 'GET') {
        return $data;
    }

    $rawInput = file_get_contents('php://input');
    if ($rawInput === false || trim($rawInput) === '') {
        return $data;
    }

    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $decoded = json_decode($rawInput, true);
        return is_array($decoded) ? $decoded : [];
    }

    if (!empty($data)) {
        return $data;
    }

    parse_str($rawInput, $parsed);
    return is_array($parsed) ? $parsed : [];
}

function request_action(): string
{
    $payload = request_data();
    $explicitAction = $payload['action'] ?? $_GET['action'] ?? null;

    if (is_string($explicitAction) && $explicitAction !== '') {
        return strtolower($explicitAction);
    }

    return '';
}
