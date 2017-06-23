<?php
namespace Gap\Routing\Exception;

class NotLoginException extends \Exception {

    protected $ref_url;

    public function setRefUrl($ref_url) {
        $this->ref_url = $ref_url;
    }
    public function getRefUrl() {
        return $this->ref_url;
    }
}
