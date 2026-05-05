<?php
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {

    define('LARAVEL_START', microtime(true));

    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // Register the Composer autoloader...
    if (file_exists(__DIR__.'/../vendor/autoload.php')) {
        require __DIR__.'/../vendor/autoload.php';
    } else {
        require __DIR__.'/../../vendor/autoload.php';
    }

    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>PHP Fatal Error in public/index.php!</h1>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<h3>Trace:</h3><pre>" . $e->getTraceAsString() . "</pre>";
}
