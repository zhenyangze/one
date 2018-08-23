<?php

namespace One\Swoole;

/**
 * Class Response
 * @package One\Swoole
 * @mixin \swoole_http_response
 */
class Response extends \One\Http\Response
{

    /**
     * @var \swoole_http_response
     */
    private $httpResponse;

    /**
     * @var \swoole_http_request
     */
    protected $httpRequest;

    public function __construct(Request $request, \swoole_http_response $response)
    {
        $this->httpResponse = $response;
        $this->httpRequest = $request;
    }

    public function header($key, $val, $replace = true, $code = null)
    {
        $this->httpResponse->header($key, $val);
        if ($code) {
            $this->code($code);
        }
    }

    public function code($code)
    {
        $this->httpResponse->status($code);
    }

    public function cookie()
    {
        $this->httpResponse->cookie(...func_get_args());
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this->httpResponse,$name)){
            return $this->httpResponse->$name(...$arguments);
        }
    }

}