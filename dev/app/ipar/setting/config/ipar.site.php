<?php
$this->set('site', [
    'i' => ['host' => 'i.' . $this->get('base_host')],
    'www' => ['host' => 'www.' . $this->get('base_host')],
    'page' => ['host' => 'page.' . $this->get('base_host')],
    'api' => ['host' => 'api.' . $this->get('base_host')],
]);
