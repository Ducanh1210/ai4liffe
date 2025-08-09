<?php
// Global configuration helpers
date_default_timezone_set(env('APP_TZ', 'Asia/Ho_Chi_Minh'));

// Public path rewrite support on built-in PHP server
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . '/../public' . $path;
    if (is_file($file)) {
        return false;
    }
}


