<?php
declare(strict_types=1);
define('ROOT', dirname(__DIR__));
ini_set('display_errors', '0');
ini_set('log_errors', '1');
if (!is_dir(ROOT . '/storage')) { mkdir(ROOT . '/storage', 0775, true); }
ini_set('error_log', ROOT . '/storage/app.log');
spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $path = ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($path)) { require $path; }
    }
});
require ROOT . '/app/helpers.php';
$configFile = getenv('CMS_CONFIG') ?: ROOT . '/config/local.php';
if (!is_file($configFile)) {
    http_response_code(503);
    exit('Chưa cấu hình ứng dụng. Hãy làm theo README để tạo config/local.php.');
}
$GLOBALS['config'] = require $configFile;
date_default_timezone_set(config('timezone', 'Asia/Ho_Chi_Minh'));
if (PHP_SAPI !== 'cli') {
    $sessionDir = ROOT . '/storage/sessions/' . hash('sha256', config('db_name') . ':' . config('session_name', 'news_blog_cms'));
    if (!is_dir($sessionDir)) { mkdir($sessionDir, 0775, true); }
    session_save_path($sessionDir);
    session_name(config('session_name', 'news_blog_cms'));
    session_set_cookie_params([
        'lifetime' => 0, 'path' => config('base_path') . '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true, 'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    session_start();
    header('Content-Type: text/html; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; object-src 'none'; base-uri 'self'; frame-ancestors 'none'; form-action 'self'");
    header('Cache-Control: no-store');
}
