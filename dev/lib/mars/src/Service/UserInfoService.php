<?php
namespace Mars\Service;

class UserInfoService extends \Gap\Service\ServiceBase
{
    public function save($data)
    {
        return $this->repo('user_info')->save($data);
    }
}