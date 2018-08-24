<?php

namespace App\Controllers;

use App\Protocol\TestHttpServer;
use App\Protocol\TestWebSocket;
use One\Http\Controller;
use One\Swoole\Protocol;

class IndexController extends Controller
{
    public function index()
    {
        return 1;
    }

    public function test()
    {
        $r = $this->session()->get('a');
        $this->session()->set('a',$r + 1);
        return $r;
    }

}