<?php

namespace One;

use One\Facades\Response as FacadeResponse;

class Controller
{

    public function __construct()
    {

    }

    public function __destruct()
    {

    }

    protected function error($msg, $code)
    {
        return FacadeResponse::error($msg,$code);
    }

    /**
     * @param $data
     */
    protected function json($data)
    {
        return FacadeResponse::json($data);
    }

    /**
     * @param array $data
     * @param string $callback
     */
    protected function jsonp($data, $callback = 'callback')
    {
        return FacadeResponse::json($data,$callback);
    }

    /**
     * @param array $fields
     * @param array $data
     */
    protected function verify($fields, $data)
    {
        foreach ($fields as $v) {
            $val = array_get($data, $v);
            if ($val == null && $val == '') {
                alert("{$v}不能为空",4001);
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
        return FacadeResponse::tpl($tpl, $data);
    }

}