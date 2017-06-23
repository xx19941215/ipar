<?php
namespace Gap\Core;

class AutoloadProvider
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function make()
    {
        $autoload_compiled_path = $this->config->get('base_dir') . '/cache/autoload.php';
        if (file_exists($autoload_compiled_path)) {
            $autoload = require $autoload_compiled_path;
        }

        if (false === $this->config->get('debug')) {
            return $autoload;
        }

        $autoload = $this->build();
        var2file($autoload_compiled_path, $autoload);

        return $autoload;
    }

    public function build()
    {
        return array_merge_recursive(
            $this->extractAutoload($this->config->get('lib')),
            $this->extractAutoload($this->config->get('app'))
        );

    }

    protected function extractAutoload($config)
    {
        $autoload = [];

        foreach ($config->all() as $name => $opts) {
            if (!isset($opts['autoload'])) {
                continue;
            }

            $autoload = array_merge_recursive($autoload, $opts['autoload']);
        }
        return $autoload;
    }

}
