<?php

namespace App\Controllers;

use App\Model\User;
use One\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->json(User::find(123));
    }

    public function test()
    {
        return memory_get_usage();
    }

}