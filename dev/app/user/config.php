<?php
$this->set('app', [
    'user' => [
        'autoload' => [
            'psr-4' => [
                "User\\" => __DIR__ . '/src'
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
//$this->router->includeFile(__DIR__ . '/setting/router/router.php');
