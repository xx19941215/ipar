<?php
namespace Mars\Repo;

class UserWbTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'user_wb';
    protected $dto = 'user_wb';
    protected $fields = [
        'id' => 'int',
        'uid' => 'int',
        'wb_uid' => 'str',
        'is_bing' => 'int',
        'is_following' => 'int'
    ];

    public function validate($data)
    {
        $uid = prop($data, 'uid', '');
        if ($this->findOne(['uid' => $uid])) {
            $this->addError('account', 'already-bound');
            return false;
        }

        return true;
    }
}
