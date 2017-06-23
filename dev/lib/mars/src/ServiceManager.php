<?php
namespace Mars;

class ServiceManager {
    protected $repo_manager;

    protected $service_config;
    protected $services;

    public function __construct($service_config)
    {
        $this->service_config = $service_config;
        $this->services = [];
        //$this->repo_manager = new \Gap\Repo\RepoManager();
    }
    public function setRepoManager($repo_manager)
    {
        $this->repo_manager = $repo_manager;
        return $this;
    }
    public function get($name)
    {
        if (!isset($this->services[$name])) {
            if ($class = $this->service_config->get($name)) {
                $class = '\\' . $class;
                //$class = $class[0] === "\\" ? $class : "\\Tos\\Service\\" . $class;
                $instance = new $class();
                $instance->setName($name);
                $this->services[$name] = $instance;
            } else {
                return null;
            }
        }
        return $this->services[$name];
    }
}
