<?php
namespace Ipar\Ui;

class UserInfoController extends IparControllerBase
{
    public function save()
    {
        $post = $this->request->request;

        $data = [
            'uid' => $this->getUser()->uid,
            'gender' => $post->get('gender'),
            'birth_year' => $post->get('birth-year'),
            'birth_month' => $post->get('birth-month'),
            'birth_day' => $post->get('birth-day'),
            'profession' => $post->get('profession'),
            'residence' => $post->get('residence'),
            'address' => $post->get('address'),
            'introduction' => $post->get('introduction')
        ];

        $pack = $this->service('user_info')->save($data);

        if ($pack->ok)
            return $this->page('user/profile', [
                'user' => $this->getUser()
            ]);

        return $this->page('user/profile', [
            'errors' => $pack->getErrors(),
            'user' => $this->getUser()
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
