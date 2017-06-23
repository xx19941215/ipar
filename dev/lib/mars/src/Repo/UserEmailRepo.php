<?php
namespace Mars\Repo;

use Gap\Repo\RepoBase;

class UserEmailRepo extends RepoBase
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

    public function changeEmail($query = [])
    {
        if (!$query) {
            return 0;
        }

        $uid = prop($query, 'uid', '');
        $email = prop($query, 'email', '');

        if ($this->db->select()
            ->from('user_email')
            ->where('uid', '=', $uid, 'int')
            ->count()
        ) {
            return $this->db->update('user_email')
                ->set('email', $email)
                ->where('uid', '=', $uid)
                ->execute();

        } else {
            return $this->db->insert('user_email')
                ->value('uid', $uid)
                ->value('email', $email)
                ->execute();
        }

    }
}

