<?php
namespace Gap\Repo;

class RepoManager
{
    protected $db;
    protected $repo_config;

    public function __construct($db, $repo_config)
    {
        $this->db = $db;
        $this->repo_config = $repo_config;
    }

    public function make($repo_name)
    {
        if (!$class_name = $this->repo_config->get($repo_name)) {
            // todo
            _debug('cannot find repo: ' . $repo_name);
        }

        $class_name = '\\' . $class_name;
        $instance = new $class_name($this->db);
        //$instance->setDb($this->db);
        //$instance->bootstrap();
        return $instance;
    }
}
