<?php

namespace App\Controllers;

use App\Model\User;
use One\Controller;
use One\Facades\Session;

class IndexController extends Controller
{
    public function index()
    {
        $user = User::find(22);
        $user->teamMembers;
        return $this->json($user);
//        return User::where('user_id',22)->update(['nickname' => 'nickname22']);
    }

    public function test()
    {
//        $a = Session::get('aa');
//        if($a){
//            $a++;
//        }else{
//            $a=1;
//        }
//        Session::set('aa',$a);
        return time();
    }

}