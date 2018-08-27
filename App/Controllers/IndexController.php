<?php

namespace App\Controllers;

use One\Http\Controller;

class IndexController extends Controller
{
    public function index()
    {

        return $this->json(php_sapi_name());
    }

    public function test()
    {
        $r = $this->session()->get('a');
        $this->session()->set('a',$r + 1);
        return $r;
    }

}