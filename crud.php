<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function run_crud(string $resourceName): void
{
    $resources = app_resources();
    $resource = $resources[$resourceName] ?? null;

    if ($resource === null) {
        json_response([
            'success' => false,
            'message' => 'Recurso invalido.',
        ], 404);
    }

    try {
        $action = request_action();
        $database = db();

        match ($action) {
            'list' => list_records($database, $resourceName, $resource),
            'get' => get_record($database, $resourceName, $resource),
            'create' => create_record($database, $resourceName, $resource),
            'update' => update_record($database, $resourceName, $resource),
            'delete' => delete_record($database, $resourceName, $resource),
            default => json_response([
                'success' => false,
                'message' => 'Informe uma action valida: list, get, create, update ou delete.',
            ], 422),
        };
    } catch (Throwable $e) {
        json_response([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

function list_records(PDO $database, string $resourceName, array $resource): void
{
    $allowedFields = $resource['fields'];
    $table = $resource['table'];
    $payload = request_data();
    $filters = [];
    $params = [];

    foreach ($allowedFields as $field) {
        if (array_key_exists($field, $payload) && $payload[$field] !== '') {
            $filters[] = "{$field} = :{$field}";
            $params[$field] = $payload[$field];
        }
    }

    $limit = max(1, min(500, (int)($payload['limit'] ?? 100)));
    $offset = max(0, (int)($payload['offset'] ?? 0));
    $sql = "SELECT * FROM {$table}";

    if ($filters !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $filters);
    }

    $sql .= ' ORDER BY ' . ($resource['order_by'] ?? 'id DESC') . ' LIMIT :limit OFFSET :offset';

    $statement = $database->prepare($sql);
    foreach ($params as $key => $value) {
        $statement->bindValue(':' . $key, $value);
    }
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();

    json_response([
        'success' => true,
        'resource' => $resourceName,
        'action' => 'list',
        'data' => $statement->fetchAll(),
    ]);
}

function get_record(PDO $database, string $resourceName, array $resource): void
{
    $id = require_id();
    $statement = $database->prepare("SELECT * FROM {$resource['table']} WHERE id = :id LIMIT 1");
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $record = $statement->fetch();

    if ($record === false) {
        json_response([
            'success' => false,
            'message' => 'Registro nao encontrado.',
        ], 404);
    }

    json_response([
        'success' => true,
        'resource' => $resourceName,
        'action' => 'get',
        'data' => $record,
    ]);
}

function create_record(PDO $database, string $resourceName, array $resource): void
{
    $payload = filter_payload(request_data(), $resource['fields']);
    if ($payload === []) {
        json_response([
            'success' => false,
            'message' => 'Nenhum campo valido foi enviado.',
        ], 422);
    }

    $columns = array_keys($payload);
    $placeholders = array_map(static fn(string $field): string => ':' . $field, $columns);
    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $resource['table'],
        implode(', ', $columns),
        implode(', ', $placeholders)
    );

    $statement = $database->prepare($sql);
    foreach ($payload as $field => $value) {
        $statement->bindValue(':' . $field, $value);
    }
    $statement->execute();

    $id = (int)$database->lastInsertId();
    json_response([
        'success' => true,
        'resource' => $resourceName,
        'action' => 'create',
        'message' => 'Registro criado com sucesso.',
        'data' => fetch_by_id($database, $resource['table'], $id),
    ], 201);
}

function update_record(PDO $database, string $resourceName, array $resource): void
{
    $id = require_id();
    $payload = filter_payload(request_data(), $resource['fields']);

    if ($payload === []) {
        json_response([
            'success' => false,
            'message' => 'Nenhum campo valido foi enviado.',
        ], 422);
    }

    $sets = [];
    foreach (array_keys($payload) as $field) {
        $sets[] = "{$field} = :{$field}";
    }

    $sql = sprintf('UPDATE %s SET %s WHERE id = :id', $resource['table'], implode(', ', $sets));
    $statement = $database->prepare($sql);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    foreach ($payload as $field => $value) {
        $statement->bindValue(':' . $field, $value);
    }
    $statement->execute();

    json_response([
        'success' => true,
        'resource' => $resourceName,
        'action' => 'update',
        'message' => 'Registro atualizado com sucesso.',
        'data' => fetch_by_id($database, $resource['table'], $id),
    ]);
}

function delete_record(PDO $database, string $resourceName, array $resource): void
{
    $id = require_id();
    $statement = $database->prepare("DELETE FROM {$resource['table']} WHERE id = :id");
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    json_response([
        'success' => true,
        'resource' => $resourceName,
        'action' => 'delete',
        'message' => 'Registro removido com sucesso.',
        'data' => ['id' => $id],
    ]);
}

function require_id(): int
{
    $payload = request_data();
    $id = $payload['id'] ?? $_GET['id'] ?? null;

    $parsedId = filter_var($id, FILTER_VALIDATE_INT);
    if ($parsedId === false || $parsedId === null) {
        json_response([
            'success' => false,
            'message' => 'Parametro id invalido.',
        ], 422);
    }

    return (int)$parsedId;
}

function filter_payload(array $payload, array $allowedFields): array
{
    return array_filter(
        $payload,
        static fn(string $field): bool => in_array($field, $allowedFields, true),
        ARRAY_FILTER_USE_KEY
    );
}

function fetch_by_id(PDO $database, string $table, int $id): ?array
{
    $statement = $database->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $record = $statement->fetch();

    return is_array($record) ? $record : null;
}
