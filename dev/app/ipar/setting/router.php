<?php
$this->setCurrentApp('ipar');

$this
    ->addSite('i', 'i.' . config()->get('base_host'))
    ->addSite('www', 'www.' . config()->get('base_host'))
    ->addSite('page', 'page.' . config()->get('base_host'))
    ->addSite('api', 'api.' . config()->get('base_host'));

$this->includeDir(__DIR__ . '/router');
