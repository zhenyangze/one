<?php

namespace App\Controllers;

use One\Http\Controller;

class IndexController extends Controller
{
    public function index()
    {
<<<<<<< HEAD

        $name = $this->request->get('name');
        if($name){
            $this->server()->pushByName($name,rand(1,100));
        }
        return $this->json(['self_id' => $this->server()->worker_pid,
            'pids' => $this->server()->getWorkerPids(),'fd' => $this->server()->_fd_name]);
=======
        return $this->json($this->request->userAgent());
>>>>>>> 974c908ed80bd93e9eb79416b61c2a9eed6cf119
    }

    public function test($id1,$id2)
    {
        return "\n".__METHOD__.'-> id1='.$id1.' '.'id2='.$id2.' time='.date('Y-m-d H:i:s')."\n";
    }

}