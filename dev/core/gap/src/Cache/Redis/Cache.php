<?php

namespace Gap\Cache\Redis;

class Cache {

    private $redis = null;

    private $host = '';
    private $port = 6379;
    private $database = 0;

    private $driver = '';

    public function __construct($config) {
        $this->host = $config->get('host', '127.0.0.1');
        $this->port = $config->get('port', 6379);
        $this->database = (int) $config->get('database', 0);

        $this->redis = new \Redis();
        $this->redis->connect($this->host, $this->port);
        $this->redis->select($this->database);
    }

    public function setDriver($driver) {
        $this->driver = $driver;
    }
    public function getDriver() {
        return $this->driver;
    }

    public function hGet($hash, $key, $default = null) {
        $val = $this->redis->hGet($hash, $key);
        return $val ?: $default;
    }
    public function hGetAll($hash) {
        return $this->redis->hGetAll($hash);
    }

    public function hSet($hash, $key, $val) {
        $this->redis->hSet($hash, $key, $val);
        return $this;
    }

    public function hIncrBy($hash, $key, $val) {
        $this->redis->hIncrBy($hash, $key, $val);
        return $this;
    }

    public function get($name, $default = null) {
        $val = $this->redis->get($name);
        return $val ?: $default;
    }

    public function has($name) {
        return $this->redis->exists($name);
    }

    public function set($name, $value, $seconds = 0) {
        if ($seconds) {
            $this->redis->setex($name, $seconds, $value);
        } else {
            $this->redis->set($name, $value);
        }
        return $this;
    }

    public function incr($key)
    {
        $this->redis->incr($key);
        return $this;
    }

    public function incrBy($key, $interval)
    {
        $this->redis->incrBy($key, $interval);
        return $this;
    }

    public function decr($key)
    {
        $this->redis->decr($key);
        return $this;
    }

    public function decrBy($key, $interval)
    {
        $this->redis->decrBy($key, $interval);
        return $this;
    }

    public function remove($name) {
        return $this->redis->delete($name);
    }

    public function flushAll()
    {
        return $this->redis->flushAll();
    }

}

