<?php

return [
    'oracle' => [
        'driver'   => 'oracle',
        'tns'      => env('DB_TNS', ''),
        'host'     => env('DB_HOST', ''),
        'port'     => env('DB_PORT', '1521'),
        'database' => env('DB_DATABASE', 'erm'),
        'username' => env('DB_USERNAME', 'system'),
        'password' => env('DB_PASSWORD', 'fahs2002'),
        'charset'  => env('DB_CHARSET', 'AL32UTF8'),
        'prefix'   => env('DB_PREFIX', ''),
    ],
];
