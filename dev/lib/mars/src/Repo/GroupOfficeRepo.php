<?php

namespace Mars\Repo;

class GroupOfficeRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'group_office';
    protected $dto = 'group_office';
    protected $fields = [
        'id' => 'int',
        'gid' => 'int',
        'name' => 'str',
        'office_address' => 'str',
        'changed' => 'str',
    ];
    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->from($this->table_name)
            ->setDto($this->dto);

        if ($gid = prop($query, 'gid', '')) {
            $ssb->where('gid', '=', $gid);
        }
        $ssb->orderBy('changed', 'desc');
        return $this->dataSet($ssb);
    }

    protected function validate($data)
    {
        $name = prop($data, 'name');
        if (!is_string($name) || empty($name)) {
            $this->addError('name', 'empty');
            return false;
        }

        $name_length = mb_strlen($name);
        if (($name_length > 0 && $name_length < 2) || $name_length > 120) {
            $this->addError('name', ['out-range-%d-and-%d', 2, 120]);
            return false;
        }

        $office_address = prop($data, 'office_address');
        if (!is_string($office_address) || empty($office_address)) {
            $this->addError('office_address', 'empty');
            return false;
        }

        $office_address_length = mb_strlen($office_address);
        if (($office_address_length > 0 && $office_address_length < 2) || $office_address_length > 120) {
            $this->addError('office_address', ['out-range-%d-and-%d', 2, 120]);
            return false;
        }
    }
}

