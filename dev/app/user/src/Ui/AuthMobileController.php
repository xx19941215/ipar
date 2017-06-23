<?php
namespace User\Ui;

class AuthMobileController extends \Gap\Routing\Controller
{
    public function reg()
    {
        $session = session();
        $wx_state = md5(uniqid());
        $session->set('wx_state', $wx_state);

        return $this->page('user/mobile-reg', [
            'wx_state' => $wx_state
        ]);
    }

    public function regPost()
    {
        $post = $this->request->request;
        $code = $post->get('code');
        $phone_number = $post->get('phone_number');
        $password = $post->get('password');
        $nick = $post->get('nick');

        $pack = $this->service('user')->createMobileUser($phone_number, $password, $nick, $code);

        if ($pack->ok) {
            set_current_uid($pack->getItem('id'));
            return $this->gotoTargetUrl();
        } else {
            return $this->page('user/mobile-reg', [
                'phone_number' => $phone_number,
                'password' => $password,
                'nick' => $nick,
                'code' => $code,
                'errors' => $pack->getErrors()
            ]);
        }
    }

    public function mobileForgotPassword()
    {
        return $this->page('user/mobile-forgot-password',[
            'hide_nav' => true,
        ]);
    }

    public function mobileForgotPasswordPost()
    {
        $phone_number = $this->request->request->get('phone_number');
        $code = $this->request->request->get('code');
        $password = $this->request->request->get('password');
        $user = $this->service('user')->getUserByPhoneNumber($phone_number);
        if (!$user) {
            return $this->page('user/mobile-forgot-password', [
                'errors' => ['account' => 'account-not-existed'],
                'phone_number' => $phone_number,
                'password' => $password,
                'code' => $code,
                'hide_nav' => true,
            ]);
        }

        $validateCode = $this->service('user')->isCorrectSMSCode($phone_number, $code);
        if (!$validateCode->ok) {
            return $this->page('user/mobile-forgot-password', [
                'errors' => $validateCode->getErrors(),
                'phone_number' => $phone_number,
                'password' => $password,
                'hide_nav' => true,
            ]);
        }

        $uid = $user->uid;
        $pack = $this->service('user')->repasswordByUid($uid, $password);

        if ($pack->ok) {
            set_current_uid($uid);
            return $this->gotoTargetUrl();
        }

        return $this->page('user/mobile-forgot-password', [
            'errors' => $pack->getErrors(),
            'phone_number' => $phone_number,
            'password' => $password,
            'code' => $code,
            'hide_nav' => true,
        ]);
    }

}
