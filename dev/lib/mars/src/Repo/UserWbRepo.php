<?php
namespace Mars\Repo;

class UserWbRepo extends \Gap\Repo\RepoBase
{

    protected $user_wb_table_repo;

    public function bootstrap()
    {
        $this->user_wb_table_repo = new UserWbTableRepo($this->db);
    }

    public function findOne($query)
    {
        return $this->user_wb_table_repo->findOne($query);
    }

    public function createWbUser($data)
    {
        $this->db->beginTransaction();
        $user_repo = gap_repo_manager()->make('user');
        $reg_pack = $user_repo->createWbUser($data['nick']);

        if ($reg_pack->getErrors()) {
            $this->db->rollback();
            return $reg_pack;
        }

        $user_repo->activateUser(['uid' => $reg_pack->getItem('id')]);//激活
        $data['uid'] = $reg_pack->getItem('id');
        $bind_pack = $this->WbUserBindAccount($data);

        if (!$bind_pack->isOk()) {
            $this->db->rollback();
            return $bind_pack;
        }

        $this->db->commit();

        return $this->packItem('id', $data['uid']);
    }

    public function WbUserBindAccount($data)
    {
        return $this->user_wb_table_repo->create($data);;
    }

}
