<?php

namespace App\Model;

use One\Database\Mysql\Model;

class TeamMembers extends Model
{
    CONST TABLE = 'team_members';

    public function team()
    {
        return $this->hasOne('team_id', Team::class, 'team_id');
    }

}