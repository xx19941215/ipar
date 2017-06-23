<?php
namespace Mars\Service;

class UserNickService extends \Gap\Service\ServiceBase
{

    protected $user_service = null;

    public function bootstrap()
    {
        $this->user_repo = gap_repo_manager()->make('user');
    }

    public function getUserByZcode($zcode)
    {
        return $this->repo('user_nick')->getUserByZcode($zcode);
    }

    public function changeNick($zcode, $nick)
    {
        $nick = trim($nick);

        if ($this->user_repo->findUid(['nick' => $nick])) {
            return $this->packExists('nick');
        }

        $pack = $this->repo('user_nick')->changeNick($zcode, $nick);

        return $pack;
    }
}

