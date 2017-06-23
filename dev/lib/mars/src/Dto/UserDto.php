<?php
namespace Mars\Dto;


class UserDto {
    public $uid;
    public $privilege;
    public $zcode;
    public $nick;
    public $passhash;
    public $avt;
    public $follow_ct;
    public $is_following;
    public $emails = [];
    public $roles = [];
    public $phone;

    public $status;
    public $logined;
    public $created;
    public $changed;

    private $avt_arr = false;
    private $primary_email = '';

    protected $is_supper;
    protected $is_admin;
    protected $is_root;


    public function getUrl()
    {
        return route_url('ipar-i-home', ['zcode' => $this->zcode]);
    }

    public function getAvt($size = 'small')
    {
        if ($this->avt_arr) {
            return $this->avt_arr;
        }
        if ($this->avt) {
            $this->avt_arr = json_decode($this->avt, true);
            return $this->avt_arr;
        }
        return false;
    }

    public function getPrimaryEmail()
    {
        if (!$this->primary_email) {
            foreach ($this->emails as $email_obj) {
                if ($email_obj['is_primary']) {
                    $this->primary_email = $email_obj['email'];
                    break;
                }
            }
        }
        return $this->primary_email;

    }

    public function hasRole($roles)
    {
        if (!$roles) {
            return false;
        }
        if (!$this->roles) {
            return false;
        }
        if (is_string($roles)) {
            $roles = [$roles];
        }
        $has_role = false;
        foreach ($roles as $role) {
            if (isset($this->roles[$role])) {
                $has_role = true;
                break;
            }
        }
        return $has_role;
    }

    public function isSuper()
    {
        if ($this->is_super === null) {
            $this->is_super = ($this->privilege >= config()->get('privilege.super'));
        }
        return $this->is_super;
    }

    public function isAdmin()
    {
        if ($this->is_admin === null) {
            $this->is_admin = ($this->privilege >= config()->get('privilege.admin'));
        }
        return $this->is_admin;
    }

    public function isRoot()
    {
        if ($this->is_root === null) {
            $this->is_root = ($this->privilege == config()->get('privilege.root'));
        }
        return $this->is_root;
    }
}

