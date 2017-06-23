<?php

namespace Mars\Repo;

class JsTransRepo extends MarsRepoBase
{
    public function createJsTrans($data = [])
    {
        $key = prop($data, 'key', '');
        if (!$key) {
            return $this->packError('JsTrans', 'key-empty');
        }


        if ($this->findJsTrans($key)) {
            return $this->packError('jsTrans', 'had-exist');
        }

        if (!$this->db->insert('js_trans_key')
            ->value('key', $key)
            ->execute()
        ) {
            return $this->packError('JsTrans', 'insert-failed');
        }

        return $this->packOk();
    }

    public function findJsTrans($key)
    {
        return $this->db->select()
            ->from('js_trans_key')
            ->where('key', '=', $key)
            ->fetchOne();
    }

    public function getJsTranKeysFromDb()
    {
        return $this->db->select(['js_trans_key', 'key'])
            ->limit(999)
            ->from('js_trans_key')
            ->fetchAll();
    }
}
