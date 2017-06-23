<?php
namespace Gap\Sns\Wx;

class Wechat {
    private $appid;
    private $secret;
    private $lang;

    private $access_token;
    private $openid;

    public function __construct($appid, $secret, $lang = 'zh_CN')
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->lang = $lang;
    }

    public function accessToken($code, $grant_type = 'authorization_code')
    {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
        $url .= http_build_query([
            'appid' => $this->appid,
            'secret' => $this->secret,
            'code' => $code,
            'grant_type' => $grant_type
        ]);
        $auth = json_decode(file_get_contents($url));
        if (!$auth || !isset($auth->access_token) || !isset($auth->openid)) {
            return false;
        }

        $this->access_token = $auth->access_token;
        $this->openid = $auth->openid;
        return true;
    }

    public function getUserInfo()
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?';
        $url .= http_build_query([
            'access_token' => $this->access_token,
            'openid' => $this->openid,
            'lang' => $this->lang
        ]);
        $user_info = json_decode(file_get_contents($url));
        if ($user_info && $user_info->openid) {
            return $user_info;
        } else {
            return false;
        }
    }
}
