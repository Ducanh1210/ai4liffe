<?php
declare(strict_types=1);

// Simple PHP MVC bootstrap for FPT Polytechnic Major Advisor

// Define base directories
define('BASE_PATH', __DIR__);

// Load env first
require_once BASE_PATH . '/config/env.php';
\Env::load(BASE_PATH);

// Global config
require_once BASE_PATH . '/config/config.php';

// PSR-4 like autoloader for app namespace
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $base_dir = BASE_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize Router and define routes
use App\Core\Router;

require_once BASE_PATH . '/app/Core/Router.php';
require_once BASE_PATH . '/app/Core/Controller.php';
require_once BASE_PATH . '/app/Core/Database.php';

// Initialize DB early to ensure connectivity; will lazily connect on first call
App\Core\Database::init([
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => (int) env('DB_PORT', '3306'),
    'name' => env('DB_NAME', 'ai'),
    'user' => env('DB_USER', 'root'),
    'pass' => env('DB_PASS', ''),
    'charset' => 'utf8mb4',
]);

// Ensure assessments table exists
try {
    $assessmentModel = new App\Models\AssessmentModel();
    $assessmentModel->ensureTable();
} catch (Throwable $e) {
    // Continue; will surface on runtime pages
}

$router = new Router(BASE_PATH);

// Routes
$router->get('/', 'HomeController@index');
$router->post('/recommend', 'ResultController@recommend');
$router->get('/result/(?P<id>\\d+)', 'ResultController@show');
$router->get('/stats', 'AdminController@stats');
// Chat feature
$router->get('/chat', 'ChatController@index');
$router->post('/api/chat', 'ChatController@api');
$router->post('/api/chat-stream', 'ChatController@stream');

// Dispatch current request
$router->dispatch();

?>