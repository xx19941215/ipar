<?php
namespace Gap\Repo;

class TableRepoBase extends RepoBase
{
    protected $table_name = '';
    protected $fields = [];

    //protected $validators = []; todo

    protected $errors = [];

    public function create($data)
    {
        $this->validate($data);
        if ($this->errors) {
            return $this->packErrors($this->errors);
        }

        $isb = $this->db->insert($this->table_name);

        if (isset($this->fields['zcode'])) {
            $isb->value('zcode', prop($data, 'zcode', $this->generateZcode()));
            unset($data['zcode']);
        }

        $hit = 0;
        foreach ($this->fields as $field => $type) {
            if ($value = prop($data, $field)) {
                $isb->value($field, $value, $type);
                $hit++;
            }
        }

        if ($hit <= 0) {
            return $this->packError('data', 'error-data');
        }

        if (!$isb->execute()) {
            return $this->packError($this->table_name, 'insert-failed');
        }

        return $this->packItem('id', $this->db->lastInsertId());
    }

    public function update($query, $data)
    {
        $this->validate($data);
        if ($this->errors) {
            return $this->packErrors($this->errors);
        }

        $usb = $this->db->update($this->table_name);
        if ($this->buildWheres($usb, $query) <= 0) {
            return $this->packError('query', 'error-query');
        }
        $hit = 0;
        foreach ($this->fields as $field => $type) {
            if ($value = prop($data, $field)) {
                $usb->set($field, $value, $type);
                $hit++;
            }
        }
        if ($hit <= 0) {
            return $this->packError('data', 'error-data');
        }

        if (!$usb->execute()) {
            return $this->packError($this->table_name, 'update-failed');
        }

        return $this->packOk();
    }

    public function updateField($query, $field_name, $field_value)
    {
        if (!$field_type = prop($this->fields, $field_name)) {
            return $this->packError($field_name, 'field-not-found');
        }

        $usb = $this->db->update($this->table_name);
        if ($this->buildWheres($usb, $query) <= 0) {
            return $this->packError('query', 'error-query');
        }

        $usb->set($field_name, $field_value, $field_type);
        if (!$usb->execute()) {
            return $this->packError($this->table_name, 'field-update-failed');
        }

        return $this->packOk();
    }

    public function delete($query)
    {
        $dsb = $this->db->delete()->from($this->table_name);
        if ($this->buildWheres($dsb, $query) <= 0) {
            return $this->packError('query', 'error-query');
        }
        if (isset($this->fields['status'])) {
            $dsb->andWhere('status', '=', 0,'int');
        }
        if (!$dsb->execute()) {
            return $this->packError($this->table_name, 'delete-failed');
        }
        return $this->packOk();
    }

    public function findOne($query, $fields = [])
    {
        $ssb = $this->db->select()
            ->from($this->table_name);

        if ($this->dto) {
            $ssb->setDto($this->dto);
        }

        if ($fields) {
            $ssb->fields($fields);
        }

        $hit = 0;
        $hit = $this->buildWheres($ssb, $query);

        if ($hit <= 0) {
            return null;
        }

        return $ssb->fetchOne();
    }

    protected function buildWheres($builder, $query)
    {
        $hit = 0;
        foreach ($query as $field => $val) {
            if ($type = prop($this->fields, $field)) {
                $builder->andWhere($field, '=', $val, $type);
                $hit++;
            }
        }
        return $hit;
    }

    protected function addError($key, $error)
    {
        $this->errors[$key] = $error;
    }

    protected function reportError($key, $error)
    {
        $this->errors[$key] = $error;
        return false;
    }

    protected function validate($data)
    {
        $this->errors['validate'] = 'no-validate';
        return false;
    }
}
