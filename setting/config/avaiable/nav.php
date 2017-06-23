<?php
$this->set('nav', [
    'page' => [
        'top' => [
            'rqt-index' => ['text' => 'rqt', 'icon' => 'icon-s-rqt'],
            'invent-index' => ['text' => 'invent', 'icon' => 'icon-s-invent'],
            'msg' => ['text' => 'msg', 'icon' => 'icon-xiaoxi']
        ]
    ],
    'auth' => [
        'reg' => 'reg',
        'login' => 'login',
        //'forgot-password' => ':forgot-password'
    ],
    'invent' => [
        'main' => [
            'invent-show' => 'summary',
            //'invent-team' => ':team'
        ],
        'steps' => [
            //'invent-rqt' => ':rqt',
            'invent-feature' => 'feature',
            'invent-sketch' => 'sketch',
            'invent-appearance' => 'appearance'
        ]
    ]
]);
