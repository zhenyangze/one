<?php

namespace One\Swoole;


use One\Cache\File;
use One\Cache\Redis;

class Session
{
    private $data = [];

    private $name = '';

    private $session_id = '';

    private $time = 0;

    private $drive;

    public function __construct()
    {
        $this->name = config('session.name');

        $this->session_id = \One\Facades\Request::cookie($this->name);

        if (!$this->session_id) {
            $this->session_id = sha1(uuid());
        }

        $this->time = intval(ini_get('session.gc_maxlifetime'));

        if (config('session.drive') == 'redis') {
            $this->drive = new Redis();
        } else {
            $this->drive = new File('session');
        }

        Swoole::$response->cookie($this->name, $this->session_id, time() + $this->time);

        $this->data = $this->drive->get($this->session_id);
    }

    public function set($key, $val)
    {
        $this->data[$key] = $val;
    }

    public function get($key = null)
    {
        if ($key) {
            return array_get($this->data, $key);
        } else {
            return $this->data;
        }
    }

    public function __destruct()
    {
        $this->drive->set($this->session_id, $this->data, $this->time);
    }

}