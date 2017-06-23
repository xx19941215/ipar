<?php
namespace Gap\Service;

class ServiceManager
{
    protected $service_config;

    public function __construct($service_config)
    {
        $this->service_config = $service_config;
    }

    public function make($service_name)
    {
        if (!$class_name = $this->service_config->get($service_name)) {
            // todo
            _debug('todo cannot find service: ' . $service_name);
        }

        $class_name = '\\' . $class_name;
        $instance = new $class_name();
        $instance->bootstrap();
        return $instance;
    }
}
