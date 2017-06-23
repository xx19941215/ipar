<?php
namespace User\Rest;

class AuthMobileController extends \Gap\Routing\Controller
{

    public function phoneCheck()
    {
        $phone_number = $this->request->request->get('phone_number');
        $user = $this->service('user')->getUserByPhoneNumber($phone_number);
        if ($user) {
            return $this->packOk();
        }

        return $this->packError('phone', 'not-exist');
    }

    public function nickCheck()
    {
        $nick = $this->request->request->get('nick');
        $user = $this->service('user')->getUserByNick($nick);

        if ($user) {
            return $this->packError('nickname', 'existed');
        }

        return $this->packOk();
    }


    public function send()
    {
        $phone_number = $this->request->request->get('phone_number');
        $template_type = $this->request->request->get('template_type');
        $effective_time = intval(config()->get('sendcloud.effective_time')) * 60;
        $sms_code = $this->createSMSCode($phone_number, $effective_time);
        $result = $this->sendSMS($phone_number, $sms_code, $template_type);

        if (!property_exists($result, 'statusCode')) {
            return $this->packError('verification code', 'send fail');
        }

        if ($result->statusCode == '200') {
            return $this->packOk();
        }

        return $this->packError('verification code', 'send fail');
    }

    public function createSMSCode($phone_number, $effective_time)
    {
        $code = $this->service('user')->createSMSCode($phone_number, $effective_time);

        return $code;
    }

    public function phoneValidateCheck()
    {
        $phone = $this->request->request->get('phone');
        $code = $this->request->request->get('code');
        $uid = $this->request->request->get('uid');
        return $validateCode = $this->service('user')->isCorrectValidateSMSCode($phone, $code, $uid);
    }

    function sendSMS($phone_number, $sms_code, $template_type)
    {
        $url = config()->get('sendcloud.url');
        $effective_time = trans(config()->get('sendcloud.effective_time'));
        $smsUser = config()->get('sendcloud.smsUser');
        $templateId = config()->get('sendcloud.templateId')[$template_type][translator()->getLocaleKey()];
        $msgType = config()->get('sendcloud.msgType');

        $param = array(
            'smsUser' => $smsUser,
            'templateId' => $templateId,
            'msgType' => $msgType,
            'phone' => $phone_number,
            'vars' => '{"%smsCode%":"' . $sms_code . '","%effectiveTime%":"' . $effective_time . '"}'
        );

        $sParamStr = "";
        ksort($param);
        foreach ($param as $sKey => $sValue) {
            $sParamStr .= $sKey . '=' . $sValue . '&';
        }

        $sParamStr = trim($sParamStr, '&');
        $smskey = config()->get('sendcloud.smskey');
        $sSignature = md5($smskey . "&" . $sParamStr . "&" . $smskey);

        $param = array(
            'smsUser' => $smsUser,
            'templateId' => $templateId,
            'msgType' => $msgType,
            'phone' => $phone_number,
            'vars' => '{"%smsCode%":"' . $sms_code . '","%effectiveTime%":"' . $effective_time . '"}',
            'signature' => $sSignature
        );

        $data = http_build_query($param);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type:application/x-www-form-urlencoded',
                'content' => $data

            ));
        $context = stream_context_create($options);
        $result = file_get_contents($url, FILE_TEXT, $context);

        return json_decode($result);
    }
}
