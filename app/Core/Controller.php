<?php

namespace App\Core;

abstract class Controller
{
    protected array $requestData = [];
    protected array $queryParams = [];

    public function __construct()
    {
        $this->parseRequest();
    }

    /**
     * Parse dos dados da requisição
     */
    protected function parseRequest(): void
    {
        $this->queryParams = $_GET;

        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'PATCH'])) {
            $input = file_get_contents('php://input');
            $this->requestData = json_decode($input, true) ?? [];
        }
    }

    /**
     * Obtém dados da requisição
     */
    protected function request(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->requestData;
        }

        return $this->requestData[$key] ?? $default;
    }

    /**
     * Obtém parâmetros de query
     */
    protected function query(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->queryParams;
        }

        return $this->queryParams[$key] ?? $default;
    }

    /**
     * Obtém paginação dos query params
     */
    protected function getPagination(): array
    {
        $page = max(1, (int)($this->query('page') ?? 1));
        $perPage = (int)($this->query('per_page') ?? 50);
        $perPage = min($perPage, 100); // Máximo de 100 itens por página

        return [
            'page' => $page,
            'per_page' => $perPage,
            'offset' => ($page - 1) * $perPage,
        ];
    }

    /**
     * Valida dados obrigatórios
     */
    protected function validate(array $required): bool
    {
        foreach ($required as $field) {
            if (empty($this->requestData[$field])) {
                Response::error("Campo obrigatório: {$field}", 400);
            }
        }

        return true;
    }
}
