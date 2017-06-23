<?php
namespace Gap\Database\Pdo\SqlBuilder;

class SqlBuilderBase
{

    use Support\WhereTrait;
    use Support\JoinTrait;
    use Support\OrderTrait;
    use Support\GroupTrait;
    use Support\FieldTrait;
    use Support\TableTrait;

    protected $adapter;
    protected $helper;

    protected $sql;
    protected $limit = 10;
    protected $offset = 0;

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->helper = new \Gap\Database\Helper\SqlHelper();
    }

    public function limit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = (int) $offset;
        return $this;
    }

    public function buildLimitSql()
    {
        if (!$this->limit) {
            return '';
        }
        return " LIMIT {$this->limit}";
    }

    public function buildOffsetSql()
    {
        if (!$this->offset) {
            return '';
        }
        return " OFFSET {$this->offset}";
    }

    public function getExecutedSql()
    {
        return $this->sql;
    }

    public function bindValue($param, $value, $type = 'str')
    {
        $this->helper->bindValue($param, $value, $type);
        return $this;
    }

    public function bindParam($param, $value, $type = 'str')
    {
        $this->helper->bindParam($param, $value, $type);
        return $this;
    }

    public function getBindValues()
    {
        return $this->helper->getBindValues();
    }
}
