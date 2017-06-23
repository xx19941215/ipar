<?php
namespace Mars\Service;

class UserPasswordService extends \Gap\Service\ServiceBase
{
    protected $user_repo = null;
    protected $validator = null;

    public function bootstrap()
    {
        $this->user_repo = gap_repo_manager()->make('user');
        $this->validator = new \Mars\Validator\UserValidator();
    }

    public function getUserByZcode($zcode)
    {
        return $this->repo('user_setting')->getUserByZcode($zcode);
    }

    public function repasswordByUid($uid, $password)
    {
        if (true !== ($validated = $this->validator->validatePassword($password))) {
            return $validated;
        }
        $passhash = password_hash($password, PASSWORD_DEFAULT);
        $pack = $this->user_repo->repasshashByUid($uid, $passhash);
        if ($pack->isOk()) {
            $this->deleteCachedUser($uid);
        }
        return $pack;
    }

    protected function deleteCachedUser($uid)
    {
        $this->cache()->delete("user-$uid");
    }

}

