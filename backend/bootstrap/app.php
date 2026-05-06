<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Always return JSON for API requests
        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                ], $status);
            }
        });
    })->create();

if (isset($_SERVER['VERCEL']) || getenv('VERCEL') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL_URL'])) {
    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');
    
    $dirs = [
        '/tmp/storage/app',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/testing',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    // Use array session driver on Vercel (stateless serverless)
    $_ENV['SESSION_DRIVER'] = 'array';
    $_SERVER['SESSION_DRIVER'] = 'array';
    putenv('SESSION_DRIVER=array');

    // Add Vercel domain to Sanctum stateful domains
    $vercelUrl = $_SERVER['VERCEL_URL'] ?? $_ENV['VERCEL_URL'] ?? 'golkrie.vercel.app';
    $currentDomains = $_ENV['SANCTUM_STATEFUL_DOMAINS'] ?? '';
    $_ENV['SANCTUM_STATEFUL_DOMAINS'] = $currentDomains . ',golkrie.vercel.app,' . $vercelUrl;
    putenv('SANCTUM_STATEFUL_DOMAINS=' . $_ENV['SANCTUM_STATEFUL_DOMAINS']);
}

return $app;
