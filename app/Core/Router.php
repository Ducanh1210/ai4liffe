<?php
namespace App\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function __construct(private string $basePath)
    {
    }

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $pattern = '#^' . $path . '$#';
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        // Normalize URI for subdirectory deployments
        if (str_starts_with($uri, dirname($scriptName))) {
            $uri = substr($uri, strlen(dirname($scriptName)));
        }
        // Support /index.php/... when rewrite is not enabled
        if (str_starts_with($uri, '/index.php')) {
            $uri = substr($uri, strlen('/index.php'));
        }
        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                $this->invokeHandler($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private function invokeHandler(string $handler, array $matches): void
    {
        [$controllerName, $action] = explode('@', $handler, 2);
        $fqcn = 'App\\Controllers\\' . $controllerName;
        if (!class_exists($fqcn)) {
            http_response_code(500);
            echo 'Controller not found';
            return;
        }
        $controller = new $fqcn();
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        call_user_func([$controller, $action], $params);
    }
}


