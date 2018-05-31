<?php

namespace One\Exceptions;

use One\Facades\Log;

class HttpException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        Log::error($message, 'http', 3);
    }
}