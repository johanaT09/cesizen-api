<?php

return [

    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    // Options du client Guzzle sous-jacent
    'guzzle_server_variable' => false,
    
    // Définir les options de transport
    'transport' => \Sentry\Transport\HttpTransport::class,

];