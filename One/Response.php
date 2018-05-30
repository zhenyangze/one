<?php

namespace One;

use One\Facades\Request as FacadeRequest;

class Response
{
    /**
     * 模板中的数据
     * @var array
     */
    public $tpl_data = [];

    private $tpl = '';

    /**
     * @var array
     */
    protected $result = [
        'err' => 0, //错误码
        'msg' => '', //错误提示
        'res' => []  //返回的数据
    ];

    /**
     * @param $msg
     * @param $code
     * @return string
     */
    public function error($msg, $code = 400)
    {
        $this->result['msg'] = $msg;
        $this->result['err'] = $code;
        $this->tpl_data = $this->result;
        $this->tpl = 'error';
        return $this->result();
    }


    public function json($data, $callback = null)
    {
        $this->header('Content-type','application/json');
        $this->result['res'] = $data;
        if ($callback) {
            return $callback . '(' . json_encode($data) . ')';
        } else {
            return json_encode($data);
        }
    }

    protected function header($key,$val,$replace = true,$code = null)
    {
        header($key.':'.$val,$replace,$code);
    }

    private function result()
    {
        if (FacadeRequest::isAjax()) {
            $this->header('Content-type','application/json');
            return json_encode($this->result);
        } else {
            if (defined('_APP_PATH_VIEW_') === false) {
                return '未定义模板路径:_APP_PATH_VIEW_';
            }
            ob_start();
            extract($this->tpl_data);
            require _APP_PATH_VIEW_ . '/' . $this->tpl . '.php';
            return ob_get_clean();
        }
    }

    /**
     * @param string $m
     * @param array $args
     * @return mixed
     */
    public function redirectMethod($m, $args = [])
    {
        return call($m, $args);
    }

    /**
     * 页面跳转
     * @param $url
     * @param array $args
     */
    public function redirect($url, $args = [])
    {
        if (isset($args['time'])) {
            $this->header('Refresh' , $args['time'] . ';url=' . $url);
        } else if (isset($args['httpCode'])) {
            $this->header('Location' , $url, true, $args['httpCode']);
        } else {
            $this->header('Location' , $url, true, 302);
        }
    }

    /**
     * @param $str
     * @param array $data
     * @param bool $csrf
     * @return string
     */
    public function getUrl($str, $data = [])
    {
        $url = array_get(Router::$as_info, $str);
        if ($data) {
            $key = array_map(function ($v) {
                return '{' . $v . '}';
            }, array_keys($data));
            $data = array_map(function ($v) {
                return urlencode($v);
            }, $data);
            $url = str_replace($key, array_values($data), $url);
        }
        return $url;
    }

    /**
     * @param string $tpl
     * @param array $data
     */
    public function tpl($template, $data = [])
    {
        $this->tpl = $template;
        $this->tpl_data = $data + $this->tpl_data;
        return $this->result();
    }

}