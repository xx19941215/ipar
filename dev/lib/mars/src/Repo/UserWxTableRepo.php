<?php
namespace Mars\Repo;

class UserWxTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'user_wx';
    protected $dto = 'user_wx';
    protected $fields = [
        'id' => 'int',
        'uid' => 'int',
        'openid' => 'str',
        'unionid' => 'str',
        'is_binding' => 'int',
        'is_following' => 'int',
        'wx_nickname' => 'str',
        'wx_sex' => 'int',
        'wx_city' => 'str',
        'wx_province' => 'str',
        'wx_country' => 'str'
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
