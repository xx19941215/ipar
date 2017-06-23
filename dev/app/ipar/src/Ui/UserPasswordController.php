<?php
namespace Ipar\Ui;

class UserPasswordController extends IparControllerBase
{
    protected $user_service;

    public function bootstrap()
    {
        $this->user_service = user_service();
    }

    public function getUser()
    {
        $zcode = $this->getParam('zcode');
        if (!$this->isCurrentUser($zcode)) {
            die($this->page('404/show'));
        }
        return $this->service('user_password')->getUserByZcode($zcode);
    }

    public function isCurrentUser($zcode)
    {
        if ($zcode != user(current_uid())->zcode) {
            return false;
        }
        return true;
    }

    public function change()
    {
        $user = $this->getUser();
        if($this->noEmailAndPhone($user))
            return $this->page('user/change-password', [
                'user' => $user
            ]);

        $isSecondaryValid = session()->get($user->uid . '_is_secondary_valid');
        if(!$isSecondaryValid)
            die($this->page('404/show'));
        return $this->page('user/change-password', [
            'user' => $user
        ]);
    }

    public function noEmailAndPhone($user)
    {
        if(!isset($user->email) && !isset($user->phone))
            return true;
        return false;
    }

    public function changePost()
    {
        $post = $this->getRequestData();
        $password = $post->get('password');
        $user = $this->getUser();
        $pack = $this->service('user_password')->repasswordByUid($user->uid, $password);

        if ($pack->isOk()) {
            return $this->gotoRoute('ipar-ui-i-account-info', [
                'zcode' => $user->zcode
            ]);
        }

        return $this->page('user/change-password', [
            'user' => $user,
            'errors' => $pack->getErrors()
        ]);
    }

    public function getRequestData()
    {
        return $this->request->request;
    }
}
