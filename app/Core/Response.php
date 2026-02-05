<?php

namespace App\Core;

class Response
{
    /**
     * Retorna uma resposta de sucesso em JSON
     */
    public static function success(mixed $data, string $message = null, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => true,
            'data' => $data,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Retorna uma resposta de erro em JSON
     */
    public static function error(string $message, int $statusCode = 400, mixed $data = null): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Retorna dados com paginação
     */
    public static function paginated(array $data, int $page, int $perPage, int $total, string $message = null): void
    {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');

        $totalPages = ceil($total / $perPage);

        $response = [
            'success' => true,
            'data' => $data,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
