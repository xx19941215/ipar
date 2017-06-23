<?php
namespace Gap\Business;

class BusinessContainer extends \Gap\Service\Container {
    protected $dbs;
    protected $caches;
    protected $session;

    public function setDbs($dbs) {
        $this->dbs = $dbs;
        return $this;
    }
    public function setConfig($config) {
        $this->config = $config;
        return $this;
    }
    public function setCaches($caches) {
        $this->caches = $caches;
        return $this;
    }
    public function setSession($session) {
        $this->session = $session;
        return $this;
    }

    public function instance($id) {
        $instance = parent::instance($id);
        $instance->setBc($this);
        $instance->setDbs($this->dbs);
        $instance->setCaches($this->caches);
        $instance->setSession($this->session);
        return $instance;
    }
}
