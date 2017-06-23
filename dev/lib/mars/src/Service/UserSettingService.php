<?php
namespace Mars\Service;

class UserSettingService extends \Gap\Service\ServiceBase
{

    public function getUserByZcode($zcode)
    {
        return $this->repo('user_setting')->getUserByZcode($zcode);
    }
}

