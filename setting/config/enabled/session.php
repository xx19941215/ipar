<?php
$cache_session_host = $this->get('local.cache_session_host');
$base_host = $this->get('base_host');

$this
    ->set('session', [
        'cookie_domain' => $this->get('base_host'),
        'cookie_path' => '/',
        'cookie_lifetime' => 86400000,
        'gc_maxlifetime' => 86400000,
        'name' => 'IPARSESS',
        'save_handler' => 'redis',
        'save_path' => "tcp://127.0.0.1:6379?database=10",
    ]);
