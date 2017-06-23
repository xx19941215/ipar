<?php
namespace Gap\Business;

class Service {
    protected $bc;
    protected $dbs;
    protected $caches;
    protected $session;
    protected $errors;

    protected $db = 'default';
    protected $table;
    protected $model;

    public function getErrors() {
        return $this->errors;
    }

    public function setBc($bc) {
        $this->bc = $bc;
        return $this;
    }

    public function setDbs($dbs) {
        $this->dbs = $dbs;
        return $this;
    }
    public function setCaches($caches) {
        $this->caches = $caches;
        return $this;
    }
    public function setSession($session) {
        $this->session = $session;
        return $this;
    }

    public function getById($id) {
        return $this->table()
            ->model($this->model)
            ->where('id', '=', $id)
            ->one();
    }

    public function getByPath($path) {
        return $this->table()
            ->model($this->model)
            ->where('path', '=', $path)
            ->one();
    }

    public function multi($limit = 10, $offset = 0, $sort = []) {
        return $this->table()
            ->model($this->model)
            ->limit((int) $limit)
            ->offset((int) $offset)
            ->sort($sort ? $sort : ['id' => -1])
            ->select();
    }


    protected function db($name = '') {
        if ($name) {
            return $this->dbs->get($name);
        } else {
            return $this->dbs->get($this->db);
        }
    }
    protected function table($name = '') {
        if ($name) {
            $builder = $this->db()->table($name);
        } else {
            $builder = $this->db()->table($this->table);
        }
        if ($this->model) {
            $builder->model($this->model);
        }
        return $builder;
    }
    protected function cache($name = 'default') {
        return $this->caches->get($name);
    }
    protected function session() {
        return $this->session;
    }
    protected function service($name) {
        return $this->bc->get($name);
    }

    protected function isUnique($table, $id, $field, $val, $db = 'default') {
        $builder = $this->db($db)->table($table)->where($field, '=', $val);
        if ($id) {
            $builder->where('id', '<>', $id);
        }
        if ($builder->exists()) {
            $this->errors[] = "$field already exists in table $table";
            return false;
        }
        return true;
    }
}
