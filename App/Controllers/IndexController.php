<?php

namespace App\Controllers;

use One\Http\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->json($this->request->userAgent());
    }

    public function test($id1,$id2)
    {
        return "\n".__METHOD__.'-> id1='.$id1.' '.'id2='.$id2.' time='.date('Y-m-d H:i:s')."\n";
    }

}