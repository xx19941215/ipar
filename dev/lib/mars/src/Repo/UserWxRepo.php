<?php
namespace Mars\Repo;

class UserWxRepo extends \Gap\Repo\RepoBase
{

    protected $user_wx_table_repo;

    public function bootstrap()
    {
        $this->user_wx_table_repo = new UserWxTableRepo($this->db);
    }

    public function findOne($query)
    {
        return $this->user_wx_table_repo->findOne($query);
    }

    public function createWxUser($data)
    {
        $this->db->beginTransaction();
        $user_service = gap_service_manager()->make('user');
        $reg_pack = $user_service->createWxUser($data['nick']);

        if ($reg_pack->getErrors()) {
            $this->db->rollback();
            return $reg_pack;
        }

        $user_service->activateUser(['uid' => $reg_pack->getItem('id')]);//激活
        $data['uid'] = $reg_pack->getItem('id');
        $bind_pack = $this->WxUserBindAccount($data);

        if (!$bind_pack->isOk()) {
            $this->db->rollback();
            return $bind_pack;
        }

        $this->db->commit();

        return $this->packItem('id', $data['uid']);
    }

    public function WxUserBindAccount($data)
    {
        return $this->user_wx_table_repo->create($data);
    }

    public function updateField($query, $field, $value)
    {
        return $this->user_wx_table_repo->updateField($query, $field, $value);
    }

}
