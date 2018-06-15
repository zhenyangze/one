<?php

namespace One\Swoole;


class Request extends \One\Request
{

    private $id = '';

    public function __construct()
    {
        foreach (Swoole::$request->server as $k => $v){
            $this->server[str_replace('-','_',strtoupper($k))] = $v;
        }
        foreach (Swoole::$request->header as $k => $v){
            $this->server['HTTP_'.str_replace('-','_',strtoupper($k))] = $v;
        }
        $this->cookie = &Swoole::$request->cookie;
        $this->get = &Swoole::$request->get;
        $this->post = &Swoole::$request->post;
        $this->files = &Swoole::$request->files;
        $this->id = uuid();
    }

    /**
     * request unique id
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function res($key = null, $default = null)
    {
        return $this->getFromArr($this->get + $this->post, $key, $default);
    }

    /**
     * @return string
     */
    public function input()
    {
        return Swoole::$request->rawContent;
    }

}