<?php
namespace Gap\Message\Driver;

class SendSms {
    public $api_user;
    public $api_key;
    public $sms_code;
    public $phone_number;

    private $api_urls = [
        'send' =>  'http://sendcloud.sohu.com/smsapi/send'
    ];

    public function __construct($opts)
    {
        $this->api_user = isset($opts['api_user']) ? $opts['api_user'] : '';
        $this->api_key = isset($opts['api_key']) ? $opts['api_key'] : '';
        $this->sms_code = isset($opts['sms_code']) ? $opts['sms_code'] : '';
        $this->phone_number = isset($opts['phone_number']) ? $opts['phone_number'] : '';
    }

    public function send($data)
    {
        $api_url = $this->api_urls[$data['api_url']];
        $param = array(
            'smsUser' => $smsUser,
            'msgType' => $msgType,
            'phone' => $this->phone_number,
            'vars' => '{"%sms_code%":"' . $this->sms_code . '","%effective_time%":"' . $effective_time . '"}'
        );

        $sParamStr = "";
        ksort($param);
        foreach ($param as $sKey => $sValue) {
            $sParamStr .= $sKey . '=' . $sValue . '&';
        }

        $sParamStr = trim($sParamStr, '&');
        $smskey = $this->api_key;
        $sSignature = md5($smskey . "&" . $sParamStr . "&" . $smskey);

        $param = array(
            'smsUser' => $smsUser,
            'msgType' => $msgType,
            'phone' => $this->phone_number,
            'vars' => '{"%sms_code%":"' . $this->sms_code . '","%effective_time%":"' . $effective_time . '"}',
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
