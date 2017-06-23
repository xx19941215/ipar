---
title: Gap\Mail\Driver\SendCloud
parentTitle: Gap



# Gap\Mail\Driver\SendCloud

- file: src/lib/gap/src/Mail/Driver/SendCloud.php
- phpunit: src/lib/gap/test/Unit/SendCloudTest.php (todo later)

1. register [sendcloud](http://sendcloud.sohu.com/) account to get 'api_user', 'api_key'
2. create new template to get 'template_invoke_name'

template example:
```html
<p>Hi %name%,</p>

<p>感谢注册 ideapar.com !</p>

<p>请通过以下链接激活你的账户：</p>

<p><a href="%link%">%link%</a></p>

<p>请勿回复此邮件，如果有疑问，请联系我们：ant@ideapar.com&nbsp;</p>

<p>&nbsp;</p>

<p>- ideapar 团队</p>
```


**Example**
```php
// config
config()->set('mail', [
    'sendcloud' => [
        'api_user' => 'ant_send_ideapar',
        'api_key' => 'BGWAY4HLOGWHfhiF',
        'from' => 'no-reply@send.ideapar.com',
    ],
]);


$mailer = new \Gap\Mail\Driver\SendCloud(
    config()->get('mail.sendcloud')->all()
);
$rs = $mailer->send([
    'api_url' => 'send_template',
    'template_invoke_name' => 'active',
    'fromname' => 'no-reply',
    'to' => $email,
    'subject' => trans('mail-activate'),
    'substitution_vars' => json_encode([
        'to' => [$email],
        'sub' => [
            '%name%' => [$user->nick],
            '%link%' => [
                route_url(
                    'activate-user',
                    [],
                    [
                        'zcode' => urlencode($user->zcode),
                        //'email' => urlencode(md5($email . '-gap')),
                        'email' => $this->encodeEmail($user),
                        'token' => $this->generateToken($user),
                        'date' => time()
                    ]
                )
            ]
        ]
    ])
]);

```
