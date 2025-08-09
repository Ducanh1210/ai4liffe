<?php
namespace App\Core;

class Controller
{
    protected function view(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseUrl = rtrim(dirname($scriptName), '/');
        if ($baseUrl === '/') {
            $baseUrl = '';
        }
        $baseIndex = rtrim($scriptName, '/');
        $viewPath = BASE_PATH . '/app/Views/' . $template . '.php';
        $layoutPath = BASE_PATH . '/app/Views/layout.php';

        ob_start();
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View not found: {$template}";
        }
        $content = ob_get_clean();

        include $layoutPath;
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}


