<?php
namespace Gap\Core;

use Gap\Routing\Router;

class RouterProvider
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function make()
    {
        $router = new Router();
        $router_compiled_path = $this->config->get('base_dir') . '/cache/setting-router.php';

        if (file_exists($router_compiled_path)) {
            $router->load(require $router_compiled_path);
        }

        if (false === $this->config->get('debug')) {
            return $router;
        }

        $router = $this->build();
        var2file($router_compiled_path, $router->getAllRoutes());

        return $router;
    }

    public function build()
    {
        $router = new Router();

        foreach ($this->config->get('site')->all() as $site => $item) {
            $router->addSite($site, $item['host']);
        }

        foreach ($this->config->get('app')->all() as $app_name => $opts) {
            if (!$router_item = prop($opts, 'router')) {
                continue;
            }

            if (!$dir_item = prop($router_item, 'dir')) {
                continue;
            }

            $router->setCurrentApp($app_name);

            foreach ($dir_item as $dir) {
                $router->includeDir($dir);
            }
        }

        return $router;
    }
}
