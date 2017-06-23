<?php
namespace Mars\Repo;

use Gap\Repo\RepoBase;

class UserSettingRepo extends RepoBase
{
    public function getUserByZcode($zcode)
    {
        $ssb = $this->db
            ->select(
                ['i', '*'],
                ['wx', '*'],
                ['u', '*'],
                ['e', 'email'],
                ['p', 'phone']
            )
            ->from(['user', 'u'])
            ->where(['u', 'zcode'], '=', $zcode)
            ->leftJoin(
                ['user_email', 'e'],
                ['e', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->leftJoin(
                ['user_phone', 'p'],
                ['p', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->leftJoin(
                ['user_info', 'i'],
                ['i', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->leftJoin(
                ['user_wx', 'wx'],
                ['wx', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->setDto('user')
            ->fetchOne();

        return $ssb;
    }
}

