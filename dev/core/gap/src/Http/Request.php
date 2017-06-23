<?php
namespace Gap\Http;



class Request extends \Symfony\Component\HttpFoundation\Request {

    //protected $session_config;

    /*
    public function setSessionConfig($session_config)
    {
        $this->session_config = $session_config;
        return $this;
    }
     */

    /*
    public function getSession()
    {
        if (!$this->hasSession()) {
        }
        return $this->session;
    }
     */
    /*
    public function refreshToken()
    {
        $this->session->set('_token', md5(time()));
        //$session->set('_token', password_hash(uniqid(time()), PASSWORD_DEFAULT));
    }
     */

    /*
    public function setUser($user)
    {
        $this->getSession()->set('user', json_encode($user));
        return $this;
    }
    public function getUser()
    {
        if ($data = $this->getSession()->get('user')) {
            return new \Tos\Dto\UserDto(json_decode($data, true));
        } else {
            return false;
        }
    }
    public function getUid()
    {
        if ($user = $this->getUser()) {
            return $user->uid;
        } else {
            return false;
        }
    }
     */
    /*
    public function getUid()
    {
        return $this->getSession()->get('uid');
    }
    public function setUid($uid)
    {
        $this->getSession()->set('uid', $uid);
        return $this;
    }
     */
}
