<?php
namespace Gap\Cache;

class CacheManager {
    protected $cache_config;
    protected $caches;

    public function __construct($cache_config) {
        $this->cache_config = $cache_config;
        $this->caches = [];
    }
    public function get($name) {
        if (!isset($this->caches[$name])) {
            if ($opts = $this->cache_config->get($name)) {
                if ('redis' == $opts->get('adapter', 'redis')) {
                    $host = $opts->get('host', '127.0.0.1');
                    $port = (int) $opts->get('port', 6379);
                    $database = (int) $opts->get('database', 0);

                    $redis = new \Redis();
                    $redis->connect($host, $port);
                    $redis->select($database);
                    $this->caches[$name] = $redis;
                }
            } else {
                return null;
            }
        }
        return $this->caches[$name];
    }
}
