<?php
namespace Gap\Http\Session;

class Session
{
    public function __construct($opts = [])
    {
        $save_path = prop($opts, 'save_path', 'tcp://127.0.0.1:6379?database=10');

        ini_set('session.save_handler', prop($opts, 'save_handler', 'redis'));
        ini_set('session.save_path', $save_path);
        ini_set('session.cookie_domain', prop($opts, 'cookie_domain'));
        ini_set('session.cookie_path', prop($opts, 'cookie_path'));
        ini_set('session.cookie_lifetime', prop($opts, 'cookie_lifetime', 0));
        ini_set('session.gc_maxlifetime', prop($opts, 'gc_maxlifetime', 14400));
        ini_set('session.name', prop($opts, 'name', 'tecsess'));

        // ini_get_all to check ini_set
        // print_r(ini_get_all('session'));
    }

    public function start()
    {
        return session_start();
    }

    public function has($name)
    {
        return (isset($_SESSION[$name]) && $_SESSION[$name]);
    }

    public function get($name, $default = '')
    {
        if (isset($_SESSION)) {
            return prop($_SESSION, $name, $default);
        }

        return $default;
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    public function clear()
    {
        $_SESSION = array();
        return session_destroy();
    }

    public function isStarted()
    {
        return PHP_SESSION_NONE !== session_status();
    }

    public function getId()
    {
        return session_id();
    }

    public function setId($id)
    {
        session_id($id);
    }

    public function getName()
    {
        return session_name();
    }
}
