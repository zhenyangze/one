<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 下午4:26
 */

namespace App\Protocol;


use App\Exceptions\Handler;
use One\Exceptions\HttpException;
use One\Http\Router;
use One\Http\RouterException;
use One\Swoole\HttpServer;

class AppHttpServer extends HttpServer
{
    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        $req = new \One\Swoole\Request($request);
        $res = new \One\Swoole\Response($req, $response);
        try {
            $router = new Router();
            list($req->class, $req->method, $mids, $action, $req->args) = $router->explain($req->method(), $req->uri(), $req, $res);
            $f = $router->getExecAction($mids, $action, $res);
            $data = $f();
        } catch (\One\Exceptions\HttpException $e) {
            $data = Handler::render($e);
        } catch (RouterException $e) {
            $data = Handler::render(new HttpException($res, $e->getMessage(), $e->getCode()));
        } catch (\Throwable $e) {
            $data = $e->getMessage();
        }
        if ($data) {
            $response->write($data);
        }
        $response->end();
    }
}