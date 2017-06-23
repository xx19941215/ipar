<?php
namespace User\Ui;

class AuthWxController extends \Gap\Routing\Controller
{
    public function login()
    {
        $session = session();

        $code = $this->request->query->get('code');
        $state = $this->request->query->get('state');
        $wx_state = $session->get('wx_state');

        if (!$code || !$state || $state != $wx_state) { //判断用户是否在微信登陆成功
            return $this->gotoRoute('login');
        }

        $get_access_token = $this->getAccessToken($code);

        if (isset($get_access_token->errcode)) {
            return $this->gotoRoute('login');
        }

        $access_token = $get_access_token->access_token;
        $openid = $get_access_token->openid;

        if (!$access_token || !$openid) {//判断是否获取到access_token
            return $this->gotoRoute('login');
        }

        $userinfo = '';
        while (!$userinfo) {
            $userinfo = $this->getWxUserInfo($access_token, $openid);
        }

        $unionid = $userinfo->unionid;
        $session->set('access_token', $access_token);
        $session->set('openid', $openid);
        $session->set('unionid', $unionid);
        $session->set('nickname', $userinfo->nickname);
        $session->set('headimgurl', $userinfo->headimgurl);
        $session->set('wx_sex', $userinfo->sex);
        $session->set('wx_city', $userinfo->city);
        $session->set('wx_province', $userinfo->province);
        $session->set('wx_country', $userinfo->country);

        $user_wx = $this->service('user_wx')->findOne([
            'unionid' => $unionid
        ]);

        if ($user_wx) {
            set_current_uid($user_wx->uid);
            return $this->gotoTargetUrl();
        } else {
            $haveOpenid = $this->service('user_wx')->findOne([
                'openid' => $openid
            ]);
            if ($haveOpenid) {
                $this->savaUnionid($unionid, $haveOpenid->uid);
                return $this->gotoTargetUrl();
            }
            return $this->page('user/wx-login', [
                'nickname' => $userinfo->nickname,
                'headimgurl' => $userinfo->headimgurl,
            ]);
        }
    }

    public function bindWx()
    {
        $session = session();

        $code = $this->request->query->get('code');
        $state = $this->request->query->get('state');
        $wx_state = $session->get('wx_state');

        if (!$code || !$state || $state != $wx_state) { //判断用户是否在微信登陆成功
            return $this->gotoRoute('login');
        }

        $get_access_token = $this->getAccessToken($code);

        if (isset($get_access_token->errcode)) {
            return $this->gotoRoute('login');
        }

        $access_token = $get_access_token->access_token;
        $openid = $get_access_token->openid;

        if (!$access_token || !$openid) {//判断是否获取到access_token
            return $this->gotoRoute('login');
        }

        $userinfo = '';
        while (!$userinfo) {
            $userinfo = $this->getWxUserInfo($access_token, $openid);
        }

        $unionid = $userinfo->unionid;
        $session->set('access_token', $access_token);
        $session->set('openid', $openid);
        $session->set('unionid', $unionid);
        $session->set('nickname', $userinfo->nickname);
        $session->set('headimgurl', $userinfo->headimgurl);
        $session->set('wx_sex', $userinfo->sex);
        $session->set('wx_city', $userinfo->city);
        $session->set('wx_province', $userinfo->province);
        $session->set('wx_country', $userinfo->country);

        $user_wx = $this->service('user_wx')->findOne([
            'unionid' => $unionid
        ]);
        $user = $this->getUser();
        $zcode = user(Current_uid())->zcode;
        if ($user_wx) {
            $this->packError('wx', 'existed');
            $url = route_url('ipar-ui-i-third-party-account-binding', ['zcode' => $zcode]) . '?wx_errors=' . trans('wechat_exist');
            header("location: " . $url);
        } else {
            $haveOpenid = $this->service('user_wx')->findOne([
                'openid' => $openid
            ]);
            if ($haveOpenid) {
                $this->savaUnionid($unionid, $haveOpenid->uid);
                return $this->page('user/third-party-account-binding', [
                    'user' => $user,
                    'wx_errors' => ['wx', 'existed'],
                ]);
            }
            $data = $this->getRequestData();
            $data['uid'] = current_uid();
            $pack = $this->service('user_wx')->WxUserBindAccount($data);

            if ($pack->isOk()) {
                return $this->gotoRoute('ipar-ui-i-third-party-account-binding', ['zcode' => $zcode]);
            }

            return $this->page('user/third-party-account-binding', [
                'user' => $user,
                'wx_errors' => $pack->getErrors()
            ]);

        }
    }

    public function getUser()
    {
        $zcode = user(current_uid())->zcode;
        if (!$this->isCurrentUser($zcode)) {
            die($this->page('404/show'));
        }
        return $this->service('user_setting')->getUserByZcode($zcode);
    }

    public function isCurrentUser($zcode)
    {
        if ($zcode != user(current_uid())->zcode) {
            return false;
        }
        return true;
    }

    public function bind()
    {
        $session = session();

        return $this->page('user/bind-account', [
            'headimgurl' => $session->get('headimgurl'),
            'nickname' => $session->get('nickname'),
        ]);
    }

    public function bindPost()
    {
        $session = session();
        $data = $this->getRequestData();
        $login_pack = $this->service('user')->login($data['email'], $data['password']);

        if (!$login_pack->isOk()) {
            return $this->page('user/bind-account', [
                'headimgurl' => $session->get('headimgurl'),
                'nickname' => $session->get('nickname'),
                'openid' => $session->get('openid'),
                'errors' => $login_pack->getErrors(),
            ]);
        }

        $data['uid'] = $login_pack->getItem('user')->uid;
        $bind_pack = $this->service('user_wx')->WxUserBindAccount($data);

        if (!$bind_pack->isOk()) {
            return $this->page('user/bind-account', [
                'headimgurl' => $session->get('headimgurl'),
                'nickname' => $session->get('nickname'),
                'openid' => $session->get('openid'),
                'errors' => $bind_pack->getErrors(),
            ]);
        }

        set_current_uid($login_pack->getItem('user')->uid);
        return $this->gotoTargetUrl();
    }

    public function reg()
    {
        $session = session();
        $data = $this->getRequestData();
        $data['nick'] = $session->get('nickname') . '_' . rand(1000, 9999);
        $headimgurl = $session->get('headimgurl');
        $pack = $this->service('user_wx')->createWxUser($data);

        if (!$pack->isOk()) {
            return $this->gotoRoute('login');
        }

        set_current_uid($pack->getItem('id'));
        $this->getWxHeadImg($headimgurl);

        return $this->gotoTargetUrl();
    }

    protected function getAccessToken($code)
    {
        $isWeChat = $this->isWeChat();
        $appId = $isWeChat ? config()->get('wechat.service.app_id') : config()->get('wechat.open.app_id');
        $appSecret = $isWeChat ? config()->get('wechat.service.app_secret') : config()->get('wechat.open.app_secret');

        return json_decode(
            file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='
                . $appId . '&secret=' . $appSecret . '&code=' . $code . '&grant_type=authorization_code')
        );
    }

    protected function isWeChat()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    protected function getWxUserInfo($access_token, $openid)
    {
        return json_decode(
            file_get_contents(
                'https://api.weixin.qq.com/sns/userinfo?access_token='
                . $access_token
                . '&openid=' . $openid . ''
            )
        );
    }

    public function getWxHeadImg($headimgurl)
    {
        $url = $headimgurl;
        $header = array(
            "Connection: Keep-Alive",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Pragma: no-cache",
            "Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3",
            "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:29.0) Gecko/20100101 Firefox/29.0"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');

        $content = curl_exec($ch);
        $curlinfo = curl_getinfo($ch);

        curl_close($ch);

        if ($curlinfo['http_code'] != 200) {
            return false;
        }

        switch ($curlinfo['content_type']) {
            case 'image/jpeg':
                $exf = '.jpg';
                break;
            case 'image/png':
                $exf = '.png';
                break;
            case 'image/gif':
                $exf = '.gif';
                break;
        }
        $filename = date("YmdHis") . md5(uniqid()) . $exf;
        $filepath = '/tmp/' . $filename;
        $res = file_put_contents($filepath, $content);
        $this->updateUserAvt($filepath);
    }

    public function updateUserAvt($filepath)
    {
        $img_file = new \Symfony\Component\HttpFoundation\File\File($filepath);
        $img_tool = image_tool();
        $pack = $img_tool->save($img_file);
        $image = $pack->getItem('image');

        $image->resize('small', ['w' => 42, 'h' => 42]);
        $image->resize('medium', ['w' => 115, 'h' => 115]);

        $avt = [
            'site' => config()->get('img.site'),
            'dir' => $image->dir,
            'name' => $image->name,
            'ext' => $image->ext
        ];

        $user_service = $this->service('user');
        if ($user_service->updateAvt($avt)->isOk()) {
            return $this->packItem('avt_url', img_src($avt, 'medium'));
        }
        return $this->packErrors($user_service->getErrors());
    }

    protected function getRequestData()
    {
        $session = session();
        $post = $this->request->request;
        return [
            'email' => $post->get('email'),
            'password' => $post->get('password'),
            'headimgurl' => $session->get('headimgurl'),
            'openid' => $session->get('openid'),
            'unionid' => $session->get('unionid'),
            'wx_nickname' => $session->get('nickname'),
            'wx_sex' => $session->get('wx_sex'),
            'wx_city' => $session->get('wx_city'),
            'wx_province' => $session->get('wx_province'),
            'wx_country' => $session->get('wx_country')
        ];
    }

    protected function savaUnionid($unionid, $uid)
    {
        return $this->service('user_wx')->updateField(['uid' => $uid], 'unionid', $unionid);
    }

    public function unbindWxFromUser()
    {
        $pack = $this->service('user')->unbindWxFromUser($this->getUser());

        if ($pack->ok) {
            return $this->page('user/third-party-account-binding', [
                'user' => $this->getUser()
            ]);
        }

        return $this->page('user/third-party-account-binding', [
            'user' => $this->getUser(),
            'wx_errors' => $pack->getErrors()
        ]);
    }
}
