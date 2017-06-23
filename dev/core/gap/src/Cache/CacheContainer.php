<?php
namespace Gap\Cache;

class CacheContainer extends \Gap\Service\Container {
    public function add($id, $opts) {
        parent::add($id, function () use ($opts) {
            if ($opts['adapter'] === 'redis') {
                $host = isset($opts['host']) ? $opts['host'] : '127.0.0.1';
                $port = isset($opts['port']) ? (int) $opts['port'] : 6379;
                $database = isset($opts['database']) ? (int) $opts['database'] : 0;

                $redis = new \Redis();
                $redis->connect($host, $port);
                $redis->select($database);

                return new Redis\Cache($redis);
            }

        });
        return $this;
    }
}
