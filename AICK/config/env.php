<?php

class Env
{
    public static function load(string $path): void
    {
        $file = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($file)) return;
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            [$key, $val] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $val = trim($val);
            if ($val !== '' && $val[0] === '"' && substr($val, -1) === '"') {
                $val = substr($val, 1, -1);
            }
            $_ENV[$key] = $val;
            putenv($key . '=' . $val);
        }
    }
}

function env(string $key, ?string $default = null): ?string
{
    $val = $_ENV[$key] ?? getenv($key);
    return $val !== false && $val !== null ? $val : $default;
}


