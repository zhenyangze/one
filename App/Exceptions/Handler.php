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
        $this->code($e->getCode());
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


    private function code($code)
    {
        switch ($code) {
            case 404:
                header('HTTP/1.1 404 Not Found');
                break;
            case 403:
                header('HTTP/1.1 403 Forbidden');
                break;
            case 401:
                header('HTTP/1.1 401 Unauthorized');
                break;
        }

    }
}