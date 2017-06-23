<?php
namespace Ipar\Ui;

class UserSettingController extends IparControllerBase
{

    public function accountInfo()
    {
        $user = $this->getUser();
        $isSecondaryValid = session()->get($user->uid . '_is_secondary_valid');
        return $this->page('user/account-info', [
            'user' => $user,
            'isSecondaryValid' => $isSecondaryValid
        ]);
    }

    public function thirdPartyAccountBinding()
    {
        $user = $this->getUser();
        $session = session();
        $wx_state = md5(uniqid());
        $session->set('wx_state', $wx_state);
        $wb_state = md5(uniqid());
        $session->set('wb_state', $wb_state);
        $isSecondaryValid = session()->get($user->uid . '_is_secondary_valid');

        return $this->page('user/third-party-account-binding', [
            'user' => $user,
            'wx_state' => $wx_state,
            'isSecondaryValid' => $isSecondaryValid
        ]);
    }
    

    public function profile()
    {
        $user = $this->getUser();
        return $this->page('user/profile', [
            'user' => $user
        ]);
    }

    public function isCurrentUser($zcode)
    {
        if ($zcode != user(current_uid())->zcode) {
            return false;
        }
        return true;
    }

    public function getUser()
    {
        $zcode = $this->getParam('zcode');
        if (!$this->isCurrentUser($zcode)) {
            die($this->page('404/show'));
        }
        return $this->service('user_setting')->getUserByZcode($zcode);
    }
}
