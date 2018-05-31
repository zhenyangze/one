<?php

namespace One\Exceptions;

use One\Facades\Log;
use Throwable;

class HttpException extends \Exception
{
    public function __construct(string $message = "", int $code = 0,  $previous = null)
    {
        parent::__construct($message,$code);
        Log::error($message,'http',1);
    }
}