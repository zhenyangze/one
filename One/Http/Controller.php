<?php

namespace One\Http;

use One\Exceptions\HttpException;
use One\Facades\Log;
use One\Swoole\Protocol;

class Controller
{

    /**
     * @var Request
     */
    protected $request = null;

    /**
     * @var Response
     */
    protected $response = null;


    /**
     * Controller constructor.
     * @param $request
     * @param $response
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function __destruct()
    {
        Log::flushTraceId();
    }

    /**
     * 执行控制器方法
     * @param $action
     * @param array $args
     * @throws HttpException
     * @return string
     */
    final public function run($action, $args = [])
    {
        if (method_exists($this, $action)) {
            try {
                return $this->$action(...$args);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), $e->getCode());
            }
        } else {
            $this->error('not find', 404);
        }
    }

    /**
     * @return Session
     */
    final protected function session()
    {
        return $this->response->session();
    }

    /**
     * @return Protocol
     */
    final protected function server()
    {
        return Protocol::getServer();
    }

    /**
     * 异常处理
     * @param $msg
     * @param int $code
     * @throws HttpException
     */
    final protected function error($msg, $code = 400)
    {
        throw new HttpException($this->response, $msg, $code);
    }

    /**
     * @param $data
     * @return string
     */
    final protected function json($data)
    {
        return formatJson($data, 0, $this->request->id());
    }

    /**
     * @param $data
     * @param string $callback
     * @return string
     */
    final protected function jsonP($data, $callback = 'callback')
    {
        return $callback . '(' . formatJson($data, 0, $this->request->id()) . ')';
    }

    /**
     * 检查必填字段
     * @param array $fields
     * @param array $data
     * @throws HttpException
     */
    final protected function verify($fields, $data)
    {
        foreach ($fields as $v) {
            $val = array_get($data, $v);
            if ($val === null || $val == '') {
                $this->error("{$v}不能为空");
            }
        }
    }

    /**
     * 模板渲染
     * @param string $tpl 模板
     * @param array $data
     * @return string
     * @throws HttpException
     */
    final protected function display($tpl, $data = [])
    {
        $dir = strtolower(substr(get_called_class(), 16, -10));
        return $this->response->tpl($dir . '/' . $tpl, $data);
    }

}