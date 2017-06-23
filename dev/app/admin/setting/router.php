<?php
$this->setCurrentApp('admin');
$this->addSite('admin', 'admin.' . config()->get('base_host'));
$this->includeDir(__DIR__ . '/router');
