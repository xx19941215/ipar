<?php
namespace Mars\Service;

class UserWxService extends UserService
{
    protected $user_wx;

    public function bootstrap()
    {
        $this->user_wx = gap_repo_manager()->make('user_wx');
    }

    public function findOne($query)
    {
        return $this->user_wx->findOne($query);
    }

    public function createWxUser($data)
    {
        return $this->user_wx->createWxUser($data);
    }

    public function WxUserBindAccount($data)
    {
        return $this->user_wx->WxUserBindAccount($data);
    }

    public function updateField($query, $field, $value)
    {
        return $this->user_wx->updateField($query, $field, $value);
    }

}
