<?php

namespace App\Controllers;

use App\Model\User;
use One\Http\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $user = User::whereIn('user_id',[341,284,1,5124,3,8,9,11])->with('teamMembers')->findAll();
        return $this->json($user);
    }

    public function test()
    {
        $r = $this->session()->get('a');
        $this->session()->set('a',$r + 1);
        return $r;
    }

}