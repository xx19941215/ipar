<?php
$this->set('app', [
    'ipar' => [
        'autoload' => [
            'psr-4' => [
                "Ipar\\" => __DIR__ . '/src'
            ]
        ],
        'router' => [
            'dir' => [
                __DIR__ . '/setting/router'
            ]
        ]
    ]
]);
$this->includeDir(__DIR__ . '/setting/config');
//$this->router->includeFile(__DIR__ . '/setting/router.php');

