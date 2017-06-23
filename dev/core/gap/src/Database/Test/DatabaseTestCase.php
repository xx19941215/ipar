<?php
namespace Gap\Database\Test;

abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    static private $pdo = null;

    private $conn = null;

    public function getConnection()
    {
        if ($this->conn) {
            return $this->conn;
        }

        if (self::$pdo === null) {
            self::$pdo = new \PDO($GLOBALS['db_dsn'], $GLOBALS['db_user'], $GLOBALS['db_password']);
        }
        $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['db_dbname']);
        return $this->conn;
    }
}
