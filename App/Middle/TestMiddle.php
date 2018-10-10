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
    public function a($next, $response)
    {
        if ($response->getHttpRequest()->args[0] <= 1) {
            return 'id 必须大于 1';
        }
        return "\n" . __METHOD__ . $next() . __METHOD__ . "\n";
    }

    public function b($next, $response)
    {
        if ($response->getHttpRequest()->args[0] <= 2) {
            return "\n" . 'id 必须大于 2' . "\n";
        }
        return "\n" . __METHOD__ . $next() . __METHOD__ . "\n";

    }

    public function c($next, $response)
    {
        if ($response->getHttpRequest()->args[0] <= 3) {
            return "\n" . 'id 必须大于 3' . "\n";
        }
        return "\n" . __METHOD__ . $next() . __METHOD__ . "\n";
    }
}