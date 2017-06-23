<?php
$base_dir = $this->get('base_dir');


$this
    ->set('debug', false)
    ->set('lib', [
        'mars' => [
            'dir' => $base_dir . '/dev/lib/mars'
        ],
        'ipar/search' => [
            'dir' => $base_dir . '/dev/lib/ipar/search'
        ]
    ])
    ->set('app', [
        'admin' => [
            'dir' => $base_dir . '/dev/app/admin'
        ],
        'user' => [
            'dir' => $base_dir . '/dev/app/user'
        ],
        'ipar' => [
            'dir' => $base_dir . '/dev/app/ipar'
        ]
    ]);

$this->includeFile(__DIR__ . '/config.local.php');
