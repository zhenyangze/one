<?php

namespace App\Controllers;

use App\Model\User;
use One\Controller;
use One\Facades\Session;

class IndexController extends Controller
{
    public function index()
    {
        $user = User::whereIn('user_id',[22,32,44,55])->with('teamMembers.team')->findAll();
        return $this->json($user);
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