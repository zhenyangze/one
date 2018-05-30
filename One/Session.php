<?php

namespace One;


class Session
{
    private $data = [];

    public function __construct()
    {
        session_name(config('session.name'));
        $time = intval(ini_get('session.gc_maxlifetime'));
        if (config('session.drive') == 'redis') {
            session_set_save_handler(new \One\Cache\SessionHandler($time), true);
        }
        session_start();
        setcookie(session_name(), session_id(), time() + $time);
        $this->data = $_SESSION;
    }

    public function set($key, $val)
    {
        $this->data[$key] = $val;
    }

    public function get($key = null)
    {
        if($key){
            return array_get($this->data, $key);
        }else{
            return $this->data;
        }
    }

    public function __destruct()
    {
        $_SESSION = $this->data;
    }
}