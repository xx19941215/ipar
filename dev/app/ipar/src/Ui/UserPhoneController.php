<?php
namespace Ipar\Ui;

class UserPhoneController extends IparControllerBase
{
    public function bind()
    {
        $user = $this->getUser();

        if($this->noEmailAndPhone($user)){
            return $this->page('user/bind-phone', [
                'user' => $user
            ]);
        }
        $isSecondaryValid = session()->get($user->uid . '_is_secondary_valid');
        if(!$isSecondaryValid)
            die($this->page('404/show'));
        return $this->page('user/bind-phone', [
            'user' => $user
        ]);
    }

    public function noEmailAndPhone($user)
    {
        if(!isset($user->email) && !isset($user->phone))
            return true;
        return false;
    }

    public function bindPost()
    {
        $user = $this->getUser();
        $post = $this->request->request;
        $code = $post->get('code');
        $phone_number = $post->get('phone_number');

        $pack = $this->service('user')->bindPhoneNumber($user->uid, $phone_number, $code);
        if ($pack->isOk())
        {
            return $this->gotoRoute('ipar-ui-i-account-info',['zcode' => $user->zcode]);
        }
        return $this->page('user/bind-phone', [
            'user' => $user,
            'errors' => $pack->getErrors(),
            'code' => $code,
            'phone_number' => $phone_number
        ]);
    }

    public function getUser()
    {
        $zcode = $this->getParam('zcode');
        if (!$this->isCurrentUser($zcode)) {
            die($this->page('404/show'));
        }
        return $this->service('user_email')->getUserByZcode($zcode);
    }

    public function isCurrentUser($zcode)
    {
        if ($zcode != user(current_uid())->zcode) {
            return false;
        }
        return true;
    }

}
