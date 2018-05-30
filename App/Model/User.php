<?php

namespace App\Model;

use One\Database\Mysql\Model;

class User extends Model
{
    CONST TABLE = 'users';

    public function events()
    {
        return [
            'afterFind' => function ($ret) {

            },
            'beforeFind' => function ($a) {

            }
        ];
    }

    public function teamMembers()
    {
        return $this->hasMany('user_id', TeamMembers::class, 'user_id');
    }

}