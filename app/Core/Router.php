<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private string $prefix = '';

    /**
     * Registra uma rota GET
     */
    public function get(string $path, string $controller, string $method): self
    {
        return $this->registerRoute('GET', $path, $controller, $method);
    }

    /**
     * Registra uma rota POST
     */
    public function post(string $path, string $controller, string $method): self
    {
        return $this->registerRoute('POST', $path, $controller, $method);
    }

    /**
     * Registra uma rota PUT
     */
    public function put(string $path, string $controller, string $method): self
    {
        return $this->registerRoute('PUT', $path, $controller, $method);
    }

    /**
     * Registra uma rota DELETE
     */
    public function delete(string $path, string $controller, string $method): self
    {
        return $this->registerRoute('DELETE', $path, $controller, $method);
    }

    /**
     * Define um prefixo para as rotas
     */
    public function prefix(string $prefix, callable $callback): self
    {
        $previousPrefix = $this->prefix;
        $this->prefix = $previousPrefix . '/' . ltrim($prefix, '/');
        $callback($this);
        $this->prefix = $previousPrefix;
        return $this;
    }

    /**
     * Registra uma rota
     */
    private function registerRoute(string $method, string $path, string $controller, string $controllerMethod): self
    {
        $fullPath = $this->prefix . '/' . ltrim($path, '/');
        $fullPath = '/' . ltrim($fullPath, '/');

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'pattern' => $this->pathToRegex($fullPath),
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
        ];

        return $this;
    }

    /**
     * Converte um path com parâmetros para regex
     *
     * Garante escape de caracteres e usa delimitador seguro (#) para evitar
     * erros de 'Unknown modifier' quando o path contém '/'.
     */
    private function pathToRegex(string $path): string
    {
        // Substitui temporariamente parâmetros por tokens para não escapá-los
        $tokened = preg_replace_callback('/\{([^}]+)\}/', function ($m) {
            return '§§' . $m[1] . '§§';
        }, $path);

        // Escapa o restante do path
        $escaped = preg_quote($tokened, '#');

        // Restaura tokens para named groups (aceitando números por padrão)
        $pattern = preg_replace('/§§([^§]+)§§/', '(?P<$1>[0-9]+)', $escaped);

        // Retorna o regex completo com delimitadores e flag unicode
        return '#^' . $pattern . '$#u';
    }

    /**
     * Encontra uma rota e executa
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                $this->executeRoute($route, $matches);
                return;
            }
        }

        Response::error('Rota não encontrada', 404);
    }

    /**
     * Executa a rota encontrada
     */
    private function executeRoute(array $route, array $matches): void
    {
        $controllerClass = 'App\\Controllers\\' . $route['controller'];
        $controllerMethod = $route['controllerMethod'];

        if (!class_exists($controllerClass)) {
            Response::error('Controller não encontrado: ' . $controllerClass, 500);
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $controllerMethod)) {
            Response::error('Método não encontrado: ' . $controllerMethod, 500);
        }

        // Remove matches com índice inteiro (resultado completo)
        $params = array_filter($matches, fn($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);

        call_user_func_array([$controller, $controllerMethod], $params);
    }
}
