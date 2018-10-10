<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/10
 * Time: 17:34
 */

namespace App\Middle;



class TestMiddle
{
    public function a($next,$response)
    {
//        print_r($response->getHttpRequest());
        return "\n".__METHOD__.$next().__METHOD__."\n";
    }

    public function b($next,$response)
    {
        return "\n".__METHOD__.$next().__METHOD__."\n";

    }

    public function c($next,$response)
    {
        return "\n".__METHOD__.$next().__METHOD__."\n";
    }
}