<?php
namespace Gap\Database;

class DatabaseContainer extends \Gap\Service\Container {
    public function add($id, $opts) {
        parent::add($id, function () use ($opts) {
            if ($opts['adapter'] === 'pdo') {
                //$opts = config()->get('db.mysql');

                $dsn = $opts['driver']
                    . ':host=' . $opts['host']
                    . ((isset($opts['port'])) ? (';port=' . $opts['port']) : '')
                    . ';dbname=' . $opts['database']
                    . ';charset=' . ((isset($opts['charset'])) ? $opts['charset'] : 'utf8mb4');

                $username = $opts['username'];
                $password = $opts['password'];

                $pdo = new \Pdo(
                    $dsn,
                    $username,
                    $password,
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_PERSISTENT => false
                    ]
                );

                return new Pdo\PdoAdapter($pdo);
            }
        });
        return $this;
    }
}
