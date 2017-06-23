<?php
$base_dir = realpath(__DIR__ . '/../');
$loader = require $base_dir . '/dev/vendor/autoload.php';

\Gap\Application::console([
    'base_dir' => $base_dir,
    'loader' => $loader
]);

function build_js_config()
{
    $config = config();
    $apps = $config->get('app')->all();

    $router = id(new \Gap\Core\RouterProvider($config))->build();
    $rest_routes = $router->getRestRoutes();

    $js_config = [
        'base_host' => $config->get('base_host'),
        'api_host' => $config->get('site.api.host'),
        'static_host' => $config->get('site.static.host'),
        'debug' => $config->get('debug'),
        'route' => $rest_routes,
    ];

    $base_dir = $config->get('base_dir');
    $js_config_path = $base_dir . '/dev/front/js/config_local/z.config.js';
    file_put_contents($js_config_path, implode([
        'module.exports = ',
        json_encode($js_config),
        ';'
    ]));
}

function exec_gulp($args)
{
    $base_dir = config()->get('base_dir');
    chdir($base_dir . '/dev/front/');

    $cmd = 'gulp ' . implode(' ', $args);
    passthru("bash -c '$cmd --color=always'");
}
