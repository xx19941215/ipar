<?php
namespace User\Ui;

class AuthWbController extends \Gap\Routing\Controller
{
    public function login()
    {
        $session = session();

        $code = $this->request->query->get('code');
        $state = $this->request->query->get('state');
        $wb_state = $session->get('wb_state');

        if (!$code || !$state || $state != $wb_state) {
            return $this->gotoRoute('login');
        }

        $keys = array();
        $keys['code'] = $code;
        $keys['redirect_uri'] = config()->get("wb.open.wb_callback_url");

        $wbakey = config()->get("wb.open.app_key");
        $wbskey = config()->get("wb.open.app_secret");
        //实例化OAuth认证对象
        $wbauth = new \SaeTOAuthV2($wbakey, $wbskey);

        try {
            $token = $wbauth->getAccessToken('code', $keys );

        } catch (OAuthException $e) {
            return $this->gotoRoute('login');
        }

        if (isset($token['errcode'])) {
            return $this->gotoRoute('login');
        }

        $access_token = $token['access_token'];
        $wb_uid = $token['uid'];

        if (!isset($access_token) || !isset($wb_uid)) {
            return $this->gotoRoute('login');
        }

        //实例化新浪微博操作类V2对象
        $client = new \SaeTClientV2($wbakey, $wbskey, $access_token);

        $get_uid = $client->get_uid();

        if (!isset($get_uid['uid'])) {
            return $this->gotoRoute('login');
        }

        $wbuser_uid = $get_uid['uid'];
        $wbuser_message = $client->show_user_by_id($wb_uid);

        $session->set('wbaccess_token', $access_token);
        $session->set('wbuser_uid', $wbuser_uid);
        $session->set('wbuser_message', $wbuser_message);

        return $this->gotoRoute("wb-login-suggest");
    }

    public function loginsuggest()
    {

        $session = session();
        $wbuser_uid = $session->get('wbuser_uid');
        $wbuser_message = $session->get('wbuser_message');
        $user_wb = $this->service('user_wb')->findOne([
            'wb_uid' => $wbuser_uid
        ]);

        if ($user_wb) {
            set_current_uid($user_wb->uid);
            return $this->gotoTargetUrl();
        }

        return $this->page('user/wb-login', [
            'nickname' => $wbuser_message['name'],
            'headimgurl' => $wbuser_message['profile_image_url'],
            'wb_uid' => $wbuser_uid
        ]);
    }
    public function bind()
    {
        $session = session();
        $wbakey = config()->get("wb.open.app_key");
        $wbskey = config()->get("wb.open.app_secret");
        $client = new \SaeTClientV2($wbakey, $wbskey, $session->get('wbaccess_token'));
        $wbuser_message = $client->show_user_by_id($session->get('wbuser_uid'));

        return $this->page('user/bind-wbaccount', [
            'headimgurl' => $wbuser_message['profile_image_url'],
            'nickname' => $wbuser_message['name'],
            'wb_uid' => $session->get('wbuser_uid')
        ]);
    }

    public function bindPost()
    {
        $data = $this->getRequestData();
        $login_pack = $this->service('user')->login($data['email'], $data['password']);

        if (!$login_pack->isOk()) {
            return $this->page('user/bind-wbaccount', [
                'headimgurl' => $data['headimgurl'],
                'nickname' => $data['nickname'],
                'wb_uid' => $data['wb_uid'],
                'errors' => $login_pack->getErrors(),
            ]);
        }

        $data['uid'] = $login_pack->getItem('user')->uid;
        $bind_pack = $this->service('user_wb')->WbUserBindAccount($data);

        if (!$bind_pack->isOk()) {
            return $this->page('user/bind-wnaccount', [
                'headimgurl' => $data['headimgurl'],
                'nickname' => $data['nickname'],
                'wb_id' => $data['wb_uid'],
                'errors' => $bind_pack->getErrors(),
            ]);
        }

        set_current_uid($login_pack->getItem('user')->uid);

        return $this->gotoTargetUrl();
    }

    public function reg()
    {
        $session = session();
        $wbakey = config()->get("wb.open.app_key");
        $wbskey = config()->get("wb.open.app_secret");
        $wb_uid = $session->get('wbuser_uid');
        $client = new \SaeTClientV2($wbakey, $wbskey, $session->get('wbaccess_token'));
        $wbuser_message = $client->show_user_by_id($wb_uid);

        $data['nick'] = $wbuser_message['name'] . '_' . mt_rand(1000, 9999);
        $data['wb_uid'] = $wb_uid;
        $headimgurl = $wbuser_message['profile_image_url'];

        $pack = $this->service('user_wb')->createWbUser($data);

        if (!$pack->isOk()) {
            return $this->gotoRoute('login');
        }

        set_current_uid($pack->getItem('id'));
        $this->getWbHeadImg($headimgurl);

        return $this->gotoTargetUrl();
    }
    public function getWbHeadImg($headimgurl)
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
        $bucket = 'ipar-avt';

        $access_id = config()->get("nos.$bucket.NOS_ACCESS_ID");
        $access_key = config()->get("nos.$bucket.NOS_ACCESS_KEY");
        $endpoint = config()->get("nos.$bucket.NOS_ENDPOINT");
        $nos = new \Ipar\Nos\Nos($access_id, $access_key, $endpoint, $bucket);
        $res = $nos->upload($img_file);

        $avt = [
            'site' => config()->get('avt.nos_ipar_avt'),
            'dir' => $res['dir'],
            'name' => $res['name'],
            'ext' => $res['ext']
        ];

        $user_service = $this->service('user');
        if ($user_service->updateAvt($avt)->isOk()) {
            return $this->packItem('avt_url', img_src($avt, 'medium'));
        }

        return $this->packErrors($user_service->getErrors());
    }
    protected function getRequestData()
    {
        $post = $this->request->request;

        return [
            'headimgurl' => $post->get('headimgurl'),
            'nickname' => $post->get('nickname'),
            'email' => $post->get('email'),
            'password' => $post->get('password'),
            'wb_uid' => $post->get('wb_uid'),
        ];
    }
}
