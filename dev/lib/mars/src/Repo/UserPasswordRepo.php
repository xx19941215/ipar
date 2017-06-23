<?php
namespace Mars\Repo;

use Gap\Repo\RepoBase;

class UserPasswordRepo extends RepoBase
{
    public function repasshashByUid($uid, $passhash)
    {
        if (!$this->db->update('user')
            ->set('passhash', $passhash)
            ->where('uid', '=', $uid)
            ->execute()
        ) {
            return $this->packError('user', 'update-failed');
        }
        return $this->packOk();
    }
}

