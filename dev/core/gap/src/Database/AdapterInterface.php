<?php
namespace Gap\Database;

interface AdapterInterface {
    public function select($sql, $params = [], $model = '');
    public function one($sql, $params = [], $model = '');
    public function insert($sql, $params = []);
    public function update($sql, $params = []);
    public function delete($sql, $params = []);
    public function statement($sql, $params = []);
}
