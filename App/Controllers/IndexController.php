<?php

namespace App\Controllers;

use App\Model\User;
use One\Controller;
use One\Facades\Session;

class IndexController extends Controller
{
    public function index()
    {
        return $this->json(User::cache(0)->find(123));
    }

    public function test()
    {
        $a = Session::get('aa');
        if($a){
            $a++;
        }else{
            $a=1;
        }
        Session::set('aa',$a);
        return $a;
    }

}