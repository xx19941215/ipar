<?php

namespace Mars\Repo;

class GroupContactTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'group_contact';
    protected $dto = 'group_contact';
    protected $fields = [
        'id' => 'int',
        'gid' => 'int',
        'contact_uid' => 'int',
        'name' => 'str',
        'email' => 'str',
        'phone' => 'str',
        'roles' => 'str',
        'created' => 'str',
    ];

    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->from($this->table_name)
            ->setDto($this->dto);

        if ($gid = prop($query, 'gid', 0)) {
            $ssb->where('gid', '=', "$gid");
        }
        $ssb->orderBy('created', 'desc');
        return $this->dataSet($ssb);
    }

    protected function validate($data)
    {
        $name = prop($data, 'name');
        if (empty($name) || !is_string($name)) {
            $this->addError('name', 'empty');
            return false;
        }

        $name_length = mb_strlen($name);
        if (($name_length > 0 && $name_length < 2) || $name_length > 120) {
            $this->addError('name', ['out-range-%d-and-%d', 2, 120]);
            return false;
        }

        $phone = prop($data, 'phone');
        if (empty($phone)) {
            $this->addError('phone', 'error');
            return false;
        }

        $email = prop($data, 'email');
        if (!is_string($email)) {
            $this->addError('email', 'error');
            return false;
        }

        $gid = prop($data, 'gid');
        if (empty($gid) || !is_numeric($gid) || ($gid <= 0)) {
            $this->addError('gid', 'error');
            return false;
        }
    }
}
