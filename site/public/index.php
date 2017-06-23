<?php
$base_dir = realpath(__DIR__ . '/../../');
$loader = require $base_dir . '/dev/vendor/autoload.php';

\Gap\Application::http([
    'base_dir' => $base_dir,
    'loader' => $loader
])->run();
