<?php

namespace One\Database\Mysql;


class DbException extends \Exception
{
    public function __construct($message = "", $code = 0, $previous = null)
    {
        echo $message;
    }
}