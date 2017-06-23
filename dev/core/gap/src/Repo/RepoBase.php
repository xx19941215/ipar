<?php
namespace Gap\Repo;

class RepoBase
{
    use \Gap\Pack\PackTrait;

    protected static $last_insert_zcode;

    protected $db;

    public function __construct($db)
    {
        $this->setDb($db);

        $this->bootstrap();
    }

    public function setDb($db)
    {
        $this->db = $db;
        return $this;
    }

    public function bootstrap()
    {
    }

    protected function generateZcode()
    {
        self::$last_insert_zcode = uniqid();
        return self::$last_insert_zcode;
    }

    protected function lastInsertZcode()
    {
        return self::$last_insert_zcode;
    }

    protected function dataSet($ssb)
    {
        return new \Gap\Database\DataSet($ssb);
    }

}
