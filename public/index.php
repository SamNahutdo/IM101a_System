<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Suppress deprecation notices from bleeding into HTTP output
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', '0');

// Polyfill for mb_split if mbstring is not loaded
if (!function_exists('mb_split')) {
    function mb_split(string $pattern, string $string, int $limit = -1): array|false {
        return preg_split('/' . $pattern . '/u', $string, $limit);
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
