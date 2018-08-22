<?php

namespace One;

use One\Swoole\Response;
use One\Swoole\Session;

class Controller
{

    /**
     * @var Response
     */
    protected $request = null;

    /**
     * @var Response
     */
    protected $response = null;

    /**
     * @var null
     */
    private $session = null;


    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $request;
    }

    final public function run($action, $args = [])
    {
        if (method_exists($this, $action)) {
            $this->$action(...$args);
        } else {
            $this->error('not find', 404);
        }
    }

    /**
     * @return Session
     */
    public function session()
    {
        if(!$this->session){
            $this->session = new Session($this->response);
        }
        return $this->session;
    }

    protected function error($msg, $code = 1)
    {

    }

    /**
     * @param $data
     */
    protected function json($data)
    {

    }

    /**
     * @param array $data
     * @param string $callback
     */
    protected function jsonp($data, $callback = 'callback')
    {

    }

    /**
     * @param array $fields
     * @param array $data
     */
    protected function verify($fields, $data)
    {
        foreach ($fields as $v) {
            $val = array_get($data, $v);
            if ($val === null || $val == '') {
                alert("{$v}不能为空", 4001);
            }
        }
    }

    /**
     * @param $tpl
     * @param array $data
     * @return string
     */
    protected function display($tpl, $data = [])
    {
        $dir = strtolower(substr(get_called_class(), 16, -10));
        return FacadeResponse::tpl($dir . '/' . $tpl, $data);
    }

}