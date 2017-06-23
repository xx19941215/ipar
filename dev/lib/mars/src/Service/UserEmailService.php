<?php
namespace Mars\Service;

class UserEmailService extends \Gap\Service\ServiceBase
{
    protected $user_repo = null;
    protected $user_service = null;
    protected $validator = null;

    public function bootstrap()
    {
        $this->user_repo = gap_repo_manager()->make('user');
        $this->user_service = gap_service_manager()->make('user');
        $this->validator = new \Mars\Validator\UserValidator();
    }

    public function getUserByZcode($zcode)
    {
        return $this->repo('user_setting')->getUserByZcode($zcode);
    }

    public function verifyEmail($user, $email)
    {
        $email = trim($email);

        if (true !== ($validated = $this->validator->validateEmail($email))) {
            return $validated;
        }

        if ($this->user_repo->findUid(['email' => $email])) {
            return $this->packExists('email');
        }

        return $this->packOk();
    }

    public function changeEmailWithToken($user, $email, $token)
    {
        if (!$this->user_service->verifyToken($user, $token)) {
            return $this->packError('token', 'not-match');
        }
        $pack = $this->repo('user_email')->changeEmail(['uid' => $user->uid, 'email' => $email]);
        return $pack;
    }
}

