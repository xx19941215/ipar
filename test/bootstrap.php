<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$base_dir = realpath(__DIR__ . '/../');
$loader = require $base_dir . '/dev/vendor/autoload.php';

$app = new \Gap\Console\Application();

$app
    ->setBaseDir($base_dir)
    ->setLoader($loader)
    ->boot();

