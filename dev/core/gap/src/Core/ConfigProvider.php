<?php
namespace Gap\Core;

use Gap\Config\Config;

class ConfigProvider
{
    protected $base_dir;

    public function __construct($base_dir)
    {
        $this->base_dir = $base_dir;
    }

    public function make()
    {
        $config = new Config();
        $config_compiled_path = $this->base_dir .'/cache/setting-config.php';

        if (file_exists($config_compiled_path)) {
            $config->load(require $config_compiled_path);
        }

        if (false === $config->get('debug')) {
            return $config;
        }

        $config = $this->build();
        var2file($config_compiled_path, $config->all());

        return $config;
    }

    public function build()
    {
        $config = new Config();
        $config->set('base_dir', $this->base_dir);

        $config->includeFile($this->base_dir . '/setting/config/config.php');

        $this->includeConfigs($config, $config->get('lib')->all());
        $this->includeConfigs($config, $config->get('app')->all());

        $config->includeDir($this->base_dir . '/setting/config/enabled');
        $config->includeDir($this->base_dir . '/setting/config/local');
        return $config;
    }

    protected function includeConfigs(&$config, $items)
    {
        foreach ($items as $opts) {
            if (!$dir = prop($opts, 'dir')) {
                continue;
            }

            $file = $dir . '/config.php';
            if (!file_exists($file)) {
                continue;
            }

            $config->includeFile($file);
        }
    }
}
