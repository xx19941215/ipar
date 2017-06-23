<?php
namespace Mars\Repo;

use Gap\Repo\RepoBase;

class UserNickRepo extends RepoBase
{
    public function changeNick($zcode, $nick)
    {
        if (!$this->db->update('user')
            ->where('zcode', '=', $zcode)
            ->set('nick', $nick)
            ->execute()
        ) {
            return $this->packError('user_update', 'user update failed');
        }

        return $this->packItem('status', 1);

    }
}

