<?php

namespace One\Swoole;


class Start
{
    public function __construct(\swoole_http_request $request,\swoole_http_response $response)
    {
        $req = new Request($request);
        $res = new Response($req,$response);
//        $session = new Session($res);

    }

    public function __destruct()
    {

    }
}