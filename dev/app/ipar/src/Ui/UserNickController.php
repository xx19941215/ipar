<?php
namespace Ipar\Ui;

class UserNickController extends IparControllerBase
{
    protected $user_service;

    public function bootstrap()
    {
        $this->user_service = user_service();
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
        $isSecondaryValid = session()->get($user->uid . '_is_secondary_valid');

        if(!$isSecondaryValid)
            die($this->page('404/show'));
        return $this->page('user/change-nick', [
            'user' => $user
        ]);
    }

    public function changePost()
    {
        $nick = $this->getRequestData()->get('new-nick');
        $pack = $this->service('user_nick')->changeNick($this->getUser()->zcode, $nick);

        if ($pack->ok) {
            return $this->page('user/account-info', [
                'status' => $pack->getItem('status'),
                'user' => $this->getUser()
            ]);
        } else {
            $data['errors'] = $pack->getErrors();
            $data['user'] = $this->getUser();
            return $this->page('user/change-nick', $data);
        }
    }


    public function getRequestData()
    {
        return $this->request->request;

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
