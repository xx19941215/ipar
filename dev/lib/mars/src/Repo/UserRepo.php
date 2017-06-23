<?php
namespace Mars\Repo;

class UserRepo extends \Gap\Repo\RepoBase
{
    public function createUser($email, $passhash, $nick)
    {
        $this->db->beginTransaction();


        $now = current_date();

        if (!$this->db->insert('user')
            ->value('passhash', $passhash)
            ->value('nick', $nick)
            ->value('zcode', $this->generateZcode())
            ->value('created', $now)
            ->value('changed', $now)
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('user_insert', 'user insert failed');
        }

        $uid = $this->db->lastInsertId();

        if (!$this->db->insert('user_email')
            ->value('email', $email)
            ->value('uid', $uid, 'int')
            ->value('is_primary', 1, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('user_email_insert', 'user_email insert failed');
        }

        //$this->setCurrentUid($uid);

        $this->db->commit();
        return $this->packOk();

    }

    public function createWxUser($nick)
    {
        $now = current_date();
        $pack = $this->db->insert('user')
            ->value('nick', $nick)
            ->value('zcode', $this->generateZcode())
            ->value('created', $now)
            ->value('changed', $now)
            ->execute();
        if (!$pack) {
            return $this->packError('user_insert', 'user insert failed');
        }

        $uid = $this->db->lastInsertId();

        return $this->packItem('id', $uid);
    }

    public function createWbUser($nick)
    {
        $now = current_date();
        $pack = $this->db->insert('user')
            ->value('nick', $nick)
            ->value('zcode', $this->generateZcode())
            ->value('created', $now)
            ->value('changed', $now)
            ->execute();
        if (!$pack) {
            return $this->packError('user_insert', 'user insert failed');
        }

        $uid = $this->db->lastInsertId();

        return $this->packItem('id', $uid);
    }

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

    public function logined($uid)
    {
        $logined = current_date();
        if (!$this->db->update('user')
            ->where('uid', '=', $uid, 'int')
            ->set('logined', $logined)
            ->set('changed', $logined)
            ->execute()
        ) {
            return $this->packError('user_update', 'user update failed');
        }
        return $this->packOk();
    }

    public function assignPrivilege($uid, $privilege)
    {
        $privilege = (int)$privilege;
        if (!$this->db->update('user')
            ->where('uid', '=', $uid, 'int')
            ->set('privilege', $privilege, 'int')
            ->set('changed', current_date())
            ->execute()
        ) {
            return $this->packError('user', 'update-failed');
        }
        return $this->packOk();
    }

    public function deleteUserByUid($uid)
    {
        $this->db->beginTransaction();
        $obj = $this->db->select('status')
            ->from('user')
            ->where('uid', '=', $uid, 'int')
            ->fetchOne();
        if (!$obj) {
            return $this->packError('user', 'not-found');
        }
        if ($obj->status != 0) {
            $this->db->rollback();
            return $this->packError('user', 'not-deactivated-user');
        }

        if (!$this->db->delete('user_wx')
            ->from('user_wx')
            ->where('uid', '=', $uid, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('user_wx', 'delete-failed');
        }
        if (!$this->db->delete('user_phone')
            ->from('user_phone')
            ->where('uid', '=', $uid, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('user_phone', 'delete-failed');
        }
        if (!$this->db->delete('user_email')
            ->from('user_email')
            ->where('uid', '=', $uid, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('user_email', 'delete-failed');
        }
        if (!$this->db->delete('user_redirect')
            ->from('user_redirect')
            ->where('uid', '=', $uid, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('user_redirect', 'delete-failed');
        }
        if (!$this->db->delete('user')
            ->from('user')
            ->where('uid', '=', $uid, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('user', 'delete-failed');
        }
        $this->db->commit();
        return $this->packItem('uid', $uid);
    }

    public function activateUser($query = [])
    {
        if (!$query) {
            return $this->packError('query', 'empty');
        }

        $builder = $this->db->update('user')
            ->set('status', 1, 'int');

        $uid = (int)prop($query, 'uid', 0);

        if (!$uid) {
            if ($email = prop($query, 'email')) {
                $uid = $this->findUid(['email' => $email]);
            }
        }

        if (!$uid) {
            return $this->packError('uid', 'empty');
        }

        if (!$builder->andWhere('uid', '=', $uid, 'int')
            ->set('status', 1, 'int')
            ->execute()
        ) {
            return $this->packError('user', 'update-failed');
        }

        return $this->packItem('uid', $uid);
    }

    public function deactivateUser($query)
    {
        if (!$query) {
            return $this->packError('query', 'empty');
        }

        $uid = (int)prop($query, 'uid', 0);
        if (!$uid) {
            if ($email = prop($query, 'email')) {
                $uid = $this->findUid(['email' => $email]);
            }
        }
        if (!$uid) {
            return $this->packError('uid', 'empty');
        }

        if (!$this->db->update('user')
            ->where('uid', '=', $uid, 'int')
            ->set('status', 0, 'int')
            ->set('changed', current_date())
            ->execute()
        ) {
            return $this->packError('user', 'update-failed');
        }

        return $this->packItem('uid', $uid);
    }

    public function updateAvt($avt)
    {
        $uid = current_uid();
        if ($this->db->update('user')
            ->where('uid', '=', $uid, 'int')
            ->set('avt', json_encode($avt))
            ->set('changed', current_date())
            ->execute()
        ) {
            return $this->packItem('uid', $uid);
        }

        return $this->packError('user', 'db-update-failed');
    }

    public function findUid($query = [])
    {
        if (!$query) {
            return 0;
        }

        $builder = $this->db->select(['u', 'uid'])
            ->from(['user', 'u']);

        if ($email = prop($query, 'email', '')) {
            $builder->leftJoin(
                ['user_email', 'ue'],
                ['ue', 'uid'],
                '=',
                ['u', 'uid']
            )
                ->andWhere(['ue', 'email'], '=', $email);
        }
        if ($nick = prop($query, 'nick', '')) {
            $builder->andWhere('nick', '=', $nick);
        }
        if ($zcode = prop($query, 'zcode', '')) {
            $builder->andWhere('zcode', '=', $zcode);
        }

        if ($builder->getWheres() && $obj = $builder->fetchOne()) {
            return $obj->uid;
        }

        return 0;
    }

    public function getUserByUid($uid)
    {
        $user = $this->db->select()
            ->from('user')
            ->setDto('user')
            ->where('uid', '=', $uid, 'int')
            ->fetchOne();

        $emails = $this->db->select()
            ->from('user_email')
            ->setFetchAssoc()
            ->where('uid', '=', $uid, 'int')
            ->fetchAll();

        if ($emails) {
            $user->emails = $emails;
        }
        return $user;
    }

    public function getUserByZcode($zcode)
    {
        $user = $this->db->select()
            ->from('user')
            ->setDto('user')
            ->where('zcode', '=', $zcode)
            ->fetchOne();

        $emails = $this->db->select()
            ->from('user_email')
            ->setFetchAssoc()
            ->where('uid', '=', $user->uid, 'int')
            ->fetchAll();

        if ($emails) {
            $user->emails = $emails;
        }
        return $user;
    }

    public function schUserBd($opts = [])
    {
        $query = prop($opts, 'query', '');

        $bd = $this->db->select(
            ['u', 'uid'],
            ['u', 'nick'],
            ['u', 'avt'],
            ['u', 'zcode'],
            ['m', 'email'],
            ['m', 'is_primary'],
            ['u', 'privilege'],
            ['u', 'logined'],
            ['u', 'created'],
            ['u', 'changed'],
            ['u', 'status'],
            ['uw', 'openid'],
            ['uw', 'unionid'],
            ['up', 'phone'],
            ['wb', 'wb_uid']
        )
            ->from(['user', 'u'])
            ->leftJoin(
                ['user_email', 'm'],
                ['m', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->leftJoin(
                ['user_phone', 'up'],
                ['up', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->leftJoin(
                ['user_wx', 'uw'],
                ['uw', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->leftJoin(
                ['user_wb', 'wb'],
                ['wb', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->orderBy(['u', 'uid']);

        if ($query) {
            $bd
                ->andWhere(['u', 'nick'], 'LIKE', "%{$query}%")
                ->orWhere(['m', 'email'], 'LIKE', "%{$query}%")
                ->orWhere(['up', 'phone'], 'LIKE', "%{$query}%")
                ->orWhere(['u', 'uid'], 'LIKE', "%{$query}%");
        }
        return $bd;
    }

    public function search($query)
    {
        return $this->dataSet(
            $this->schUserBd($query)
        );
    }

    public function getUserByPhoneNumber($phone_number)
    {
        $user = $this->db->select()
            ->from(['user_phone', 'up'])
            ->select([
                'u', '*',
                'ue', '*',
                'up', '*',
                'wx', '*'
            ])
            ->setDto('user')
            ->where(['up', 'phone'], '=', $phone_number, 'int')
            ->leftJoin(
                ['user', 'u'],
                ['u', 'uid'],
                '=',
                ['up', 'uid']
            )
            ->leftJoin(
                ['user_email', 'ue'],
                ['ue', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->leftJoin(
                ['user_wx', 'wx'],
                ['ue', 'uid'],
                '=',
                ['wx', 'uid']
            )
            ->fetchOne();

        return $user;
    }

    public function createMobileUser($phone_number, $passhash, $nick)
    {
        $this->db->beginTransaction();
        $now = current_date();

        if (!$this->db->insert('user')
            ->value('passhash', $passhash)
            ->value('nick', $nick)
            ->value('zcode', $this->generateZcode())
            ->value('status', 1)
            ->value('created', $now)
            ->value('changed', $now)
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('user_insert', 'user insert failed');
        }

        $uid = $this->db->lastInsertId();

        if (!$this->db->insert('user_phone')
            ->value('phone', $phone_number)
            ->value('uid', $uid, 'int')
            ->value('is_primary', 1, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('user_phone_insert', 'user_phone insert failed');
        }

        $this->db->commit();

        return $this->packItem('id', $uid);

    }

    public function getUserByNick($nick)
    {
        $user = $this->db->select()
            ->from(['user', 'u'])
            ->select([
                'u', '*',
                'ue', '*',
                'up', '*',
                'wx', '*'
            ])
            ->setDto('user')
            ->where(['u', 'nick'], '=', $nick, '')
            ->leftJoin(
                ['user_phone', 'up'],
                ['up', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->leftJoin(
                ['user_email', 'ue'],
                ['ue', 'uid'],
                '=',
                ['u', 'uid']
            )
            ->leftJoin(
                ['user_wx', 'wx'],
                ['ue', 'uid'],
                '=',
                ['wx', 'uid']
            )
            ->fetchOne();

        return $user;
    }

    public function bindPhoneNumber($uid, $phone_number)
    {
        $obj = $this->db->select()
            ->from('user_phone')
            ->where('uid', '=', $uid, 'int')
            ->fetchOne();
        if ($obj) {
            if (!$this->db->update('user_phone')
                ->set('phone', $phone_number)
                ->where('uid', '=', $uid)
                ->execute()
            ) {
                return $this->packError('phone_number', 'update-failed');
            }
            return $this->packOk();
        }
        if (!$this->db->insert('user_phone')
            ->value('phone', $phone_number)
            ->value('uid', $uid)
            ->execute()
        ) {
            return $this->packError('phone_number', 'bind-failed');
        }
        return $this->packOk();
    }

    public function unbindWxFromUser($user)
    {
        if (!$this->db->delete('user_wx')
            ->from('user_wx')
            ->where('uid', '=', $user->uid, 'int')
            ->execute()
        ) {
            return $this->packError('user_wx', 'delete-failed');
        }

        return $this->packOk();
    }

}
