<?php
namespace Gap\Security;

class Auth {

    protected $repo;
    protected $session;

    public function __construct($repo, $session) {
        $this->repo = $repo;
        $this->session = $session;
    }

    public function getIdentity() {
        return $this->session->get('user_id');
    }

    public function getCurrentUser() {
        $identity = $this->getIdentity();
        return $this->repo->getUserById($identity);
    }

    public function getUserById($user_id) {
        return $this->repo->getUserById($user_id);
    }

    public function authenticate($email, $password) {
        if ($user = $this->repo->getAuthByEmail($email)) {
            if (password_verify($password, $user->password)) {
                $this->session->set('user_id', $user->id);
                return true;
            }
        } else {
            return false;
        }
    }

    public function logout() {
        $this->session->clear();
    }
}
