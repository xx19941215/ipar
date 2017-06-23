<?php
namespace Gap\Routing;
use Mars\PackTrait;

class ControllerBase
{
    use \Gap\Pack\PackTrait;

    protected $app;
    protected $config;
    protected $service_manager;

    public function setApp($app)
    {
        $this->app = $app;
        return $this;
    }

    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    public function bootstrap()
    {
    }

    public function service($service_name)
    {
        return $this->getServiceManager()->make($service_name);
    }

    protected function getServiceManager()
    {
        if ($this->service_manager) {
            return $this->service_manager;
        }

        $this->service_manager = new \Gap\Service\ServiceManager(
            $this->config->get('service')
        );
        return $this->service_manager;
    }

}
