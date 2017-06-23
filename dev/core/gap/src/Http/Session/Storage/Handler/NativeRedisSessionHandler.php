<?php

namespace Gap\Http\Session\Storage\Handler;

use \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler;

class NativeRedisSessionHandler extends NativeSessionHandler {
    public function __construct($savePath = '') {
        if (!extension_loaded('redis')) {
            throw new \RuntimeException('PHP does not have "redis session module registered');
        }

        if ("" === $savePath) {
            $savePath = ini_get('session.save_path');
        }
        if ("" === $savePath) {
            $savePath = "tcp://127.0.0.1:6379";
        }

        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', $savePath);
    }
}
