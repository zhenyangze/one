<?php

namespace App\Controllers;

use App\Model\User;
use One\Http\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $res = User::where('user_id','<',123)->limit(2)->findAll();
        return $this->json($res->toArray());
    }

    public function test($id1,$id2)
    {
        return "\n".__METHOD__.'-> id1='.$id1.' '.'id2='.$id2.' time='.date('Y-m-d H:i:s')."\n";
    }

}