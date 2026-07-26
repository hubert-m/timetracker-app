<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Wyłączamy middleware CSRF aby zobaczyć odpowiedź kontrolera dla błędu walidacji (email)
$app->make(Illuminate\Contracts\Http\Kernel::class)->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

$request = Illuminate\Http\Request::create(
    '/login', 'POST',
    ['email' => 'nieistnieje@email.pl', 'password' => 'qwerty'],
    [], [],
    [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
    ]
);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
