<?php

namespace App\Controllers;

use App\Model\User;
use One\Http\Controller;

class IndexController extends Controller
{
    public function index()
    {
        echo strlen(uuid()).PHP_EOL;
        echo round(memory_get_usage()/1024/1024, 2).'MB'.PHP_EOL;
        $arr = [];
        for ($i = 0; $i < 100000; $i++){
            $arr[uuid()] = 1;
        }
        echo count($arr).PHP_EOL;
        echo round(memory_get_usage()/1024/1024, 2).'MB'.PHP_EOL;
        return 1;
//        return 'hello world';
    }

    public function test()
    {
        $r = $this->session()->get('a');
        $this->session()->set('a',$r + 1);
        return $r;
    }

}