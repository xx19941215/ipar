<?php
$this
    ->set('sendcloud', [
            'url' => 'http://sendcloud.sohu.com/smsapi/send',
            'smsUser' => 'ipar',
            'smskey' => '3dmnlw2ebuO4GiOuV1iWyI4N3NVOUD61',
            'templateId' => [
                'register' => [
                    'zh-cn' => '3393',
                    'en-us' => '3392'
                ],
                'validate' => [
                    'zh-cn' => '3966',
                    'en-us' => '3603'
                ]
            ],
            'msgType' => '0',
            'effective_time' => '30-minute'
        ]
    );
