<?php

namespace Gap\Console;

use Gap\Core\ApplicationCore;

class Application extends ApplicationCore
{
    public function init($opts = [])
    {
        $this->base_dir = $opts['base_dir'];
        $this->loader = $opts['loader'];

        $this->config = id(new \Gap\Core\ConfigProvider($this->base_dir))->build();
        $this->initLoader(
            id(new \Gap\Core\AutoloadProvider($this->config))->build()
        );

        if ($this->debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        parent::init($opts);
    }
    public function run($argv = [])
    {
    }

    public function cmd($argv)
    {
        $app_class = $argv[1];
        $controller_class = $argv[2];

        $class = "\\" . ucfirst($app_class) . "\\Cmd\\" . ucfirst($controller_class) . "Cmd";

        $obj = new $class();
        $obj->setApp($this);
        $obj->setConfig($this->getConfig());
        $obj->run();
    }

    public function boot()
    {
    }
}
