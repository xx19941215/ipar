<?php
namespace Gap\Database;

class AdapterManager {
    protected $db_config;
    protected $adps;

    public function __construct($db_config) {
        $this->db_config = $db_config;
        $this->adps = [];
    }

    public function get($name) {
        if (!isset($this->adps[$name])) {
            if ($opts = $this->db_config->get($name)) {
                if ('pdo' == $opts->get('adapter', 'pdo')) {
                    $adapter = new Pdo\PdoAdapter($opts);
                    $this->adps[$name] = $adapter;
                }
            } else {
                return null;
            }
        }
        return $this->adps[$name];
    }
}
