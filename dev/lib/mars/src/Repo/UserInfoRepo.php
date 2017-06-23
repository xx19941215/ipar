<?php
namespace Mars\Repo;

class UserInfoRepo extends \Gap\Repo\RepoBase
{
    public function save($data)
    {
        if ($this->db->select()
                ->from('user_info')
                ->where('uid', '=', $data['uid'])
                ->count() == 0
        ) {
            return $this->create($data);
        } else {
            return $this->update($data);
        }
    }

    public function create($data)
    {
        $sqlbuilder = $this->db->insert('user_info');

        if (isset($data['gender']))
            $sqlbuilder->value('gender', $data['gender']);

        if (isset($data['birth_year']))
            $sqlbuilder->value('birth_year', $data['birth_year']);

        if (isset($data['birth_month']))
            $sqlbuilder->value('birth_month', $data['birth_month']);

        if (isset($data['birth_day']))
            $sqlbuilder->value('birth_day', $data['birth_day']);

        if (isset($data['profession']))
            $sqlbuilder->value('profession', $data['profession']);

        if (isset($data['residence']))
            $sqlbuilder->value('residence', $data['residence']);

        if (isset($data['address']))
            $sqlbuilder->value('address', $data['address']);

        if (isset($data['introduction']))
            $sqlbuilder->value('introduction', $data['introduction']);

        $id = $sqlbuilder->value('uid', $data['uid'])->execute();

        if ($id)
            return $this->packOk();
        return $this->packError('insert error', 'user_info insert error');
    }

    public function update($data)
    {
        $sqlbuilder = $this->db->update('user_info');

        if (isset($data['gender']))
            $sqlbuilder->set('gender', $data['gender']);

        if (isset($data['birth_year']))
            $sqlbuilder->set('birth_year', $data['birth_year']);

        if (isset($data['birth_month']))
            $sqlbuilder->set('birth_month', $data['birth_month']);

        if (isset($data['birth_day']))
            $sqlbuilder->set('birth_day', $data['birth_day']);

        if (isset($data['profession']))
            $sqlbuilder->set('profession', $data['profession']);

        if (isset($data['residence']))
            $sqlbuilder->set('residence', $data['residence']);

        if (isset($data['address']))
            $sqlbuilder->set('address', $data['address']);

        if (isset($data['introduction']))
            $sqlbuilder->set('introduction', $data['introduction']);

        $r = $sqlbuilder->where('uid', '=', $data['uid'], 'int')->execute();
        if ($r)
            return $this->packOk();
        return $this->packError('update error', 'user_info update error');
    }
}