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

    public function cookie()
    {
        setcookie(...func_get_args());
    }

    public function json($data, $callback = null)
    {
        $this->header('Content-type', 'application/json');
        $this->result['res'] = $data;
        if ($callback) {
            return $callback . '(' . json_encode($this->result) . ')';
        } else {
            return json_encode($this->result);
        }
    }

    public function header($key, $val, $replace = true, $code = null)
    {
        header($key . ':' . $val, $replace, $code);
    }

    private $status_texts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    );

    public function code($code)
    {
        if(isset($this->status_texts[$code])){
            header('HTTP/1.1 '.$code.' '.$this->status_texts[$code]);
        }
    }


    private function result()
    {
        if (FacadeRequest::isAjax()) {
            $this->header('Content-type', 'application/json');
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
            $this->header('Refresh', $args['time'] . ';url=' . $url);
        } else if (isset($args['httpCode'])) {
            $this->header('Location', $url, true, $args['httpCode']);
        } else {
            $this->header('Location', $url, true, 302);
        }
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