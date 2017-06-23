<?php
namespace Gap\Database\Pdo;


class PdoAdapter {

    protected $pdo;
    protected $trans_level = 0;

    public function __construct($config)
    {
        $driver = $config->get('driver');
        $host = $config->get('host');
        $database = $config->get('database');
        $port = $config->get('port', 3306);
        $username = $config->get('username');
        $password = $config->get('password');
        $charset = $config->get('charset', 'utf8mb4');

        $dsn = $driver
            . ':host=' . $host
            . ';port=' . $port
            . ';dbname=' . $database
            . ';charset=' . $charset;

        $pdo = new \Pdo(
                $dsn,
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_PERSISTENT => false
                ]
            );
        $this->pdo = $pdo;
    }

    public function prepare($sql)
    {
        $stmt = $this->pdo->prepare($sql);
        return new Statement($stmt);
    }

    public function query($sql)
    {
        return new Statement($this->pdo->query($sql));
    }

    public function exec($sql)
    {
        return $this->pdo->exec($sql);
    }

    public function select(...$fields)
    {
        $sql_builder = new SqlBuilder\SelectSqlBuilder($this);
        foreach ($fields as $field) {
            $sql_builder->field($field);
        }
        return $sql_builder;
    }

    public function update($table, $path = '')
    {
        $sql_builder = new SqlBuilder\UpdateSqlBuilder($this);
        $sql_builder->update($table, $path);
        return $sql_builder;
    }

    public function insert($table, $path = '')
    {
        $sql_builder = new SqlBuilder\InsertSqlBuilder($this);
        $sql_builder->insert($table, $path);
        return $sql_builder;
    }

    public function delete(...$aliases)
    {
        $sql_builder = new SqlBuilder\DeleteSqlBuilder($this);
        foreach ($aliases as $alias) {
            $sql_builder->deleteAlias($alias);
        }
        return $sql_builder;
    }

    public function beginTransaction()
    {
        $this->trans_level++;
        if ($this->trans_level > 1) {
            return true;
        }

        if (!$this->pdo->beginTransaction()) {
            _debug('db-begin-transaction-failed');
            return false;
        }
        return true;
    }

    public function commit()
    {
        if ($this->trans_level <= 0) {
            $this->trans_level = 0;
            _debug('db-commit-failed');
            return false;
        }
        $this->trans_level--;
        if ($this->trans_level === 0) {
            if (!$this->pdo->commit()) {
                _debug('db-commit-failed');
                return false;
            }
        }

        return true;
    }

    public function rollback()
    {
        if ($this->trans_level > 0) {
            if (!$this->pdo->rollback()) {
                _debug('db-rollback-failed');
                return false;
            }
            $this->trans_level = 0;
        }
        return true;
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

}
