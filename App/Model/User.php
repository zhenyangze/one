<?php

namespace App\Model;

use One\Database\Mysql\Build;
use One\Database\Mysql\Model;

class User extends Model
{
    CONST TABLE = 'users';

    protected $_cache_time = 0;

    protected $_cache_column = ['user_id'];

    public function events()
    {
        return [
            'afterFind' => function ($ret) {

            },
            'beforeFindAll' => function (Build $model, & $a) {
                $model->where('user_id', 100);
            }
        ];
    }

    public function teamMembers()
    {
        return $this->hasMany('user_id', TeamMembers::class, 'user_id');
    }

}