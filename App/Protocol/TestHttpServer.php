<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 下午4:26
 */

namespace App\Protocol;


use One\Swoole\HttpServer;

class TestHttpServer extends HttpServer
{
    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        try{
            $req = new \One\Swoole\Request($request);
            $res = new \One\Swoole\Response($req,$response);
            $data = (new \One\Http\Router())->exec($req,$res);
        }catch (\One\Exceptions\HttpException $e){
            $data = (new \App\Exceptions\Handler())->render($e);
        }catch (\Exception $e) {
            $data = $e->getMessage();
        }
        $response->write($data);
        $response->end();
    }
}