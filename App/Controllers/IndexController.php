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
        return $this->json([$id1,$id2]);
    }

}