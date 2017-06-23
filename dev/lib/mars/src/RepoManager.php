<?php
namespace Mars;

class RepoManager
{
    protected $repo_config;
    protected $repos;
    protected $cache_manager;

    public function __construct($repo_config)
    {
        $this->repo_config = $repo_config;
    }

    public function setCacheManager($cache_manager)
    {
        $this->cache_manager = $cache_manager;
        return $this;
    }

    public function get($name)
    {
        if (isset($this->repos[$name])) {
            return $this->repos[$name];
        }

        $args = $this->repo_config->get($name);
        $db_name = 'default';
        $class = $args;
        if (!is_string($class)) {
            $class = $args->get('class');
            $db_name = $args->get('db');
        }
        $class = '\\' . $class;

        $instance = new $class();
        $adapter = adapter_manager()->get($db_name);
        $instance->setAdapter($adapter);

        $instance->setName($name);

        $this->repos[$name] = $instance;
        return $this->repos[$name];
    }
}
