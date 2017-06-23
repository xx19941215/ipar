<?php
namespace Mars;

class RepoBase
{

    use \Gap\Pack\PackTrait;

    protected static $last_insert_zcode;

    protected $adapter;
    protected $db; // dprecated

    protected $repo_manager;
    protected $name = '';

    protected $flipped_types;
    protected $types;

    protected $flipped_actions;
    protected $actions;

    protected $current_uid = null;
    protected $trace = null;

    protected $is_transaction = false;


    public function setRepoManager($repo_manager)
    {
        $this->repo_manager = $repo_manager;
        return $this;
    }

    // deprecated
    public function setDb($db)
    {
        $this->setAdapter($db);
        return $this;
    }

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        $this->db = $this->adapter;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
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

    protected function begin()
    {
        //$this->trace()->start();
        $this->startTrace();
        $this->is_transaction = false;
    }

    protected function beginTransaction()
    {
        $this->startTrace();
        $this->db->beginTransaction();
        $this->is_transaction = true;
    }

    protected function commit($key = '', $val = '')
    {

        $this->endTrace();
        if ($this->is_transaction) {
            if (!$this->db->commit()) {
                _debug('server in maintenance');
            }
        }

        if ($key) {
            return $this->packItem($key, $val);
        }

        return $this->packOk();

    }

    protected function rollback($error_key = '', $error_msg = '', $extra_errors = [])
    {
        $errors = [];

        if ($error_key && $error_msg) {
            $errors[$error_key] = $error_msg;
        }
        if ($extra_errors) {
            $errors = array_merge($errors, $extra_errors);
        }


        if ($this->is_transaction) {
            $this->db->rollback();
        }

        $this->endTrace($errors);

        return $this->packErrors($errors);

    }

    protected function getCurrentUid()
    {
        /*
        if ($this->current_uid) {
            return $this->current_uid;
        }
         */

        if ($this->current_uid = user_service()->getCurrentUid()) {
            return $this->current_uid;
        }

        if ($this->current_uid = current_uid()) {
            return $this->current_uid;
        }

        return 0;
    }

    public function setCurrentUid($uid)
    {
        $this->current_uid = $uid;
    }

    protected function newDto($dto_name, $data = [])
    {
        $class = dto_manager()->get($dto_name);
        $instance = new $class();

        if ($data && is_array($data)) {
            foreach ($data as $key => $val) {
                if (property_exists($instance, $key)) {
                    $instance->$key = $val;
                }
            }
            $instance->type = $dto_name;
        }

        return $instance;
    }



    protected function startTrace()
    {
        if (!$this->trace) {
            $this->trace = trace();
        }
        $this->trace->start();
    }

    protected function endTrace($errors = [])
    {
        $this->trace->end($errors);
    }
}
