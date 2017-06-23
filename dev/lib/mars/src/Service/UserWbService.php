<?php
namespace Mars\Service;

class UserWbService extends UserService
{
    protected $user_wb;

    public function bootstrap()
    {
        $this->user_wb = gap_repo_manager()->make('user_wb');
    }

    public function findOne($query)
    {
        return $this->user_wb->findOne($query);
    }

    public function createWbUser($data)
    {
        return $this->user_wb->createWbUser($data);
    }

    public function WbUserBindAccount($data)
    {
        return $this->user_wb->WbUserBindAccount($data);
    }

}
