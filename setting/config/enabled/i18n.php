<?php
$this
    ->set('i18n', [
        'translator' => [
            'db_table' => 'trans',
        ],
        'locale' => [
            'available' => [
                'zh-cn' => ['id' => 1, 'key' => 'zh-cn', 'title' => '简体中文'],
                'en-us' => ['id' => 2, 'key' => 'en-us', 'title' => 'English'],
                'de-de' => ['id' => 3, 'key' => 'de-de', 'title' => 'Deutschland - Deutsch'],
                'fr-fr' => ['id' => 4, 'key' => 'fr-fr', 'title' => 'France - Français'],
                'zh-tw' => ['id' => 5, 'key' => 'zh-tw', 'title' => '繁體中文 - 台湾'],
                'zh-hk' => ['id' => 6, 'key' => 'zh-hk', 'title' => '繁體中文 - 香港'],
            ],
            'enabled' => [
                'zh-cn', 'en-us'
            ],
            'default' => 'zh-cn',
        ],
    ]);
