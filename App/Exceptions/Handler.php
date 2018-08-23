<?php

namespace App\Exceptions;

use One\Exceptions\HttpException;

class Handler
{
    public function render(HttpException $e)
    {
        $e->response->code($e->getCode());

        if($e->response->getHttpRequest()->isAjax()){
            return $e->response->json(formatJson($e->getMessage(),$e->getCode(),$e->response->getHttpRequest()->id()));
        }else{
            $file = _APP_PATH_VIEW_ . '/Exceptions/' . $e->getCode() . '.php';
            if (file_exists($file)) {
                return $e->response->tpl('Exceptions/'.$e->getCode(),['e' => $e]);
            }else{
                return $e->response->json(formatJson($e->getMessage(),$e->getCode(),$e->response->getHttpRequest()->id()));
            }
        }
    }

}

