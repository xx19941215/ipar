<?php
$this->set('lib', [
    'mars' => [
        'autoload' => [
            'psr-4' => [
                "Mars\\" => __DIR__ . '/src'
            ]
        ]
    ]
]);
$this->includeDir(__DIR__ . '/setting/config');
