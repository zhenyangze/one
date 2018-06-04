<?php

namespace One\Swoole;


class Request
{

    private $server = [];

    private $cookie = [];

    private $get = [];

    private $post = [];

    private $files = [];

    private $id = '';


    public function __construct()
    {
        $this->server = Swoole::$request->server;
        $this->cookie = Swoole::$request->cookie;
        $this->get = Swoole::$request->get;
        $this->post = Swoole::$request->post;
        $this->files = Swoole::$request->files;
        $this->id = uuid();
    }

    /**
     * @return string|null
     */
    public function ip()
    {
        return array_get_not_null($this->server, ['remote_addr', 'http_client_ip', 'http_x_forwarded_for']);
    }


    /**
     * @param $name
     * @return mixed|null
     */
    public function server($name)
    {
        return array_get($this->server, $name);
    }

    /**
     * @return mixed|null
     */
    public function userAgent()
    {
        return $this->server('http_user_agent');
    }

    /**
     * @return string
     */
    public function uri()
    {
        $path = urldecode(array_get_not_null($this->server, ['request_uri', 'argv.1']));
        $paths = explode('?', $path);
        return '/' . trim($paths[0], '/');
    }

    /**
     * request unique id
     * @return string
     */
    public function id()
    {
        return $this->id;
    }


    private function getFromArr($arr, $key, $default = null)
    {
        $r = array_get($arr, $key);
        if (!$r) {
            $r = $default;
        }
        return $r;

    }

    /**
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return $this->getFromArr($this->get, $key, $default);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function post($key, $default = null)
    {
        return $this->getFromArr($this->post, $key, $default);
    }

    /**
     * @param int $i
     * @return mixed|null
     */
    public function arg($i, $default = null)
    {
        global $argv;
        return $this->getFromArr($argv, $i, $default);
    }


    /**
     * @param $key
     * @return mixed|null
     */
    public function res($key, $default = null)
    {
        return $this->getFromArr($this->get + $this->post, $key, $default);
    }


    /**
     * @param $key
     * @return mixed|null
     */
    public function cookie($key, $default = null)
    {
        return $this->getFromArr($this->cookie, $key, $default);
    }

    /**
     * @return string
     */
    public function input()
    {
        return Swoole::$request->rawContent;
    }

    /**
     * @return array
     */
    public function json()
    {
        return json_decode($this->input(), true);
    }

    /**
     * @return array
     */
    public function file()
    {
        $files = [];
        foreach ($this->files as $name => $fs) {
            $keys = array_keys($fs);
            if (is_array($fs[$keys[0]])) {
                foreach ($keys as $k => $v) {
                    foreach ($fs[$v] as $name => $val) {
                        $files[$name][$v] = $val;
                    }
                }
            } else {
                $files[$name] = $fs;
            }
        }
        return $files;
    }

    /**
     * @return string
     */
    public function method()
    {
        return strtolower($this->server('request_method'));
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        if ($this->server('http_x_requested_with') == 'XMLHttpRequest') {
            return true;
        } else {
            return false;
        }
    }
}