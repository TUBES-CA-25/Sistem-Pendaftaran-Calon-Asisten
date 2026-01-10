<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Content Security Policy Headers - Permissive configuration for development
header("Content-Security-Policy: " .
    "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:; " .
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http:; " .
    "style-src 'self' 'unsafe-inline' https: http:; " .
    "font-src 'self' 'unsafe-inline' https: http: data:; " .
    "img-src 'self' 'unsafe-inline' https: http: data: blob:; " .
    "connect-src 'self' https: http:; " .
    "media-src 'self' https: http:; " .
    "object-src 'none'; " .
    "frame-ancestors 'self'; " .
    "base-uri 'self'; " .
    "form-action 'self';"
);

// Additional Security Headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");

require_once '../routes/autoload.php';

$app = new App\Core\App;
$app->run();
