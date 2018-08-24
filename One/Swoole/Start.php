<?php

namespace One\Swoole;


class Start
{
    public function __construct(\swoole_http_request $request,\swoole_http_response $response)
    {
        $req = new Request($request);
        $res = new Response($req,$response);



    }

    public function __destruct()
    {

    }
}