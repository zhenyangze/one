<?php

namespace One\Database\Mysql;


trait RelationTrait
{
    protected function hasOne($self_column, $third, $third_column)
    {
        return new HasOne($self_column, $third, $third_column, $this);
    }

    protected function hasMany($self_column, $third, $third_column)
    {
        return new hasMany($self_column, $third, $third_column, $this);
    }

}