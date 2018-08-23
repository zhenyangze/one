<?php

namespace App\Controllers;

use App\Model\User;
use One\Http\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $user = User::find(22);
        $user->teamMembers;
        return $this->json($user);
    }

    public function test()
    {
        $r = $this->session()->get('a');
        $this->session()->set('a',$r + 1);
        return $r;
    }

}