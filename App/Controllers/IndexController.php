<?php

namespace App\Controllers;

use One\Http\Controller;

class IndexController extends Controller
{
    public function index()
    {

        $name = $this->request->get('name');
        if($name){
            $this->server()->pushByName($name,rand(1,100));
        }
        return $this->json(['self_id' => $this->server()->worker_pid,
            'pids' => $this->server()->getWorkerPids(),'fd' => $this->server()->_fd_name]);
    }

    public function test()
    {
        $r = $this->session()->get('a');
        $this->session()->set('a',$r + 1);
        return $r;
    }

}