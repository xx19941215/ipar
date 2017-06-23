<?php
$this
    ->set('db', [
        'default' => [
            'adapter' => 'pdo',
            'driver' => 'mysql',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
        'i18n' => [
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'driver' => 'mysql',
        ],
    ]);

