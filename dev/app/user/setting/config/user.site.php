<?php
$base_host = $this->get('base_host');

$this->set('site', [
    'login' => ['host' => 'login.' . $this->get('base_host')],
    'logout' => ['host' => 'logout.' . $this->get('base_host')],
    'safe' => ['host' => 'safe.' . $this->get('base_host')],
    'reg' => ['host' => 'reg.' . $this->get('base_host')],
    'wx' => ['host' => 'wx.' . $this->get('base_host')],
    'mobile' => ['host' => 'mobile.' . $this->get('base_host')],
    'api' => ['host' => 'api.' . $this->get('base_host')],
    'wb' => ['host' => 'wb.' . $this->get('base_host')],
]);
