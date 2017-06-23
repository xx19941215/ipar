<?php
namespace Gap\Core;

class ApplicationCore
{
    protected $base_dir;
    protected $config;
    protected $loader;
    protected $debug = null;

    /*
    public function init($opts)
    {
         * todo optimize php by compiling all the codes into one php file
         *
        $code_compiled_path = $base_dir .'/cache/compiled.php';
        if (file_exists($code_compiled_path)) {
            require $code_compiled_path;
        }

    }
    */

    public function init($opts = [])
    {
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        date_default_timezone_set(
            $this->config->get('timezone', 'Asia/Shanghai')
        );
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    protected function var2file($target_path, $var)
    {
        file_put_contents(
            $target_path,
            '<?php return ' . var_export($var, true) . ';'
        );
        @chmod($target_path, 0777);
    }

    protected function isDebug()
    {
        return $this->debug;
    }

    protected function initLoader($autoload)
    {
        if (isset($autoload['psr-4'])) {
            foreach ($autoload['psr-4'] as $namespace => $dir) {
                $this->loader->setPsr4($namespace, [$dir]);
            }
        }
    }
}
