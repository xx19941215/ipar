<?php
namespace Gap\Service;

class ServiceBase
{
    use \Gap\Pack\PackTrait;

    protected $cache;
    protected $cache_name = 'default';

    protected $current_repo;

    public function bootstrap()
    {
    }

    public function cache()
    {
        if ($this->cache) {
            return $this->cache;
        }

        $this->cache = cache_manager()->get($this->cache_name);
        return $this->cache;
    }

    public function repo($repo_name)
    {
        $this->current_repo = repo_manager()->make($repo_name);
        return $this->current_repo;
    }
}
