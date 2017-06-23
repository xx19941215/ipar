<?php
namespace Gap\Mail\Driver;

class SendCloud {
    private $api_user;
    private $api_key;

    private $api_urls = [
        'send' =>  'http://sendcloud.sohu.com/webapi/mail.send.json',
        'send_template' => 'http://sendcloud.sohu.com/webapi/mail.send_template.json',
    ];

    public function __construct($opts)
    {
        $this->api_user = $opts['api_user'];
        $this->api_key = $opts['api_key'];
        $this->from = isset($opts['from']) ? $opts['from'] : '';
    }

    public function send($data)
    {
        $api_url = $this->api_urls[$data['api_url']];
        $params = [
            'api_user' => $this->api_user,
            'api_key' => $this->api_key,
            'from' => isset($data['from']) ? $data['from'] : $this->from,
            'fromname' => $data['fromname'],
            'to' => $data['to'],
            'subject' => $data['subject']
        ];
        if (isset($data['substitution_vars'])) {
            $params['substitution_vars'] = $data['substitution_vars'];
        }
        if (isset($data['replyto'])) {
            $params['replayto'] = $data['replyto'];
        }
        if (isset($data['template_invoke_name'])) {
            $params['template_invoke_name'] = $data['template_invoke_name'];
        }

        $data = http_build_query($params);
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data
            ]
        ];

        $context = stream_context_create($opts);
        $result = file_get_contents($api_url, FILE_TEXT, $context);

        return json_decode($result, true);
    }
}
