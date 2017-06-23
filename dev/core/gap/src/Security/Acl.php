<?php
namespace Gap\Security;

class Acl {

    protected $userService;

    public function setUserService($userService) {
        $this->userService = $userService;
        return $this;
    }
    public function isLogin() {
        if ($this->userService->getIdentity()) {
            return true;
        } else {
            return false;
        }
    }
    public function isAccess($roles) {
        _debug('todo acl isaccess');
    }
}
