<?php

namespace App\Exceptions;

use One\Exceptions\HttpException;
use One\Facades\Log;
use One\Facades\Request;
use One\Facades\Response;

class Handler
{
    public function render(\Exception $e)
    {
        Response::code($e->getCode());
        if ($e instanceof HttpException) {
            return Response::error($e->getMessage(), 4001);
        } else {
            Log::error([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'url' => Request::uri()
            ]);
            return Response::error('请求异常', 4000);
        }
    }

}