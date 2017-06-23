<?php
namespace Gap\Database\Pdo;

class Statement {

    protected static $data_type_maps = [
        'bool'  => \PDO::PARAM_BOOL,
        'int'   => \PDO::PARAM_INT,
        'str'   => \PDO::PARAM_STR,
        'null'  => \PDO::PARAM_NULL
    ];

    protected $stmt;

    public function __construct(\PDOStatement $stmt)
    {
        $this->stmt = $stmt;
        $this->stmt->setFetchMode(\PDO::FETCH_OBJ);
    }
    public function setFetchAssoc()
    {
        $this->stmt->setFetchMode(\PDO::FETCH_ASSOC);
        return $this;
    }
    public function setDto($dto = '')
    {
        if ($dto) {
            $this->stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $dto);
        }
        return $this;
    }

    public function bindValue($param, $value, $type_name = 'str')
    {
        $type_maps = self::$data_type_maps;
        $type_value = isset($type_maps[$type_name]) ? $type_maps[$type_name] : $type_maps['str'];

        $this->stmt->bindValue($param, $value, $type_value);
        return $this;
        /*
        if (is_array($value)) {
            $index = 0;
            var_dump($param);
            var_dump($value);exit();
            foreach ($value as $sub) {
                $this->bindValue("{$param}_{$index}", $sub, $type_name);
                $index++;
            }
        } else {
            $type_maps = self::$data_type_maps;
            $type_value = isset($type_maps[$type_name]) ? $type_maps[$type_name] : $type_maps['str'];

            $this->stmt->bindValue($param, $value, $type_value);
        }
        return $this;
         */
    }
    public function execute($params = [])
    {
        if ($params) {
            return $this->stmt->execute($params);
        } else {
            return $this->stmt->execute();
        }
    }
    public function fetchAll()
    {
        return $this->stmt->fetchAll();
    }
    public function fetchOne()
    {
        $rows = $this->stmt->fetchAll();
        if ($rows) {
            return $rows[0];
        } else {
            return false;
        }
    }
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

}
