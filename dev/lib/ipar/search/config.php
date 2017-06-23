<?php
$this->set('lib', [
    'ipar/search' => [
        'autoload' => [
            'psr-4' => [
                "Ipar\\Search\\" => __DIR__ . '/src'
            ]
        ]
    ]
]);
$this->includeDir(__DIR__ . '/setting/config');
