<?php

namespace One\Database\Mysql;

use One\Facades\Cache;

class CacheBuild extends Build
{
    private $cache_time = 0;

    /**
     * 缓存时间(秒)
     * @param int $time
     */
    public function cache($time)
    {
        $this->cache_time = $time;
        return $this;
    }

    private $cache_tag = [];

    public function find($id = null)
    {
        $this->limit(1);
        if ($id) {
            $this->where($this->getPriKey(), $id);
        }
        if($this->cache_time == 0){
            return parent::find();
        }
        return Cache::get($this->getCacheKey(), function () {
            return parent::find();
        }, $this->cache_time, $this->cache_tag);
    }

    public function count()
    {
        if($this->cache_time == 0){
            return parent::count();
        }
        $this->is_count = 1;
        return Cache::get($this->getCacheKey(), function () {
            return parent::count();
        }, $this->cache_time, $this->cache_tag);
    }

    public function findAll()
    {
        if($this->cache_time == 0){
            return parent::findAll();
        }
        return Cache::get($this->getCacheKey(':list'), function () {
            return parent::findAll();
        }, $this->cache_time, $this->cache_tag);
    }

    public function update($data)
    {
        $ret = parent::update($data);
        $this->flushCache();
        return $ret;
    }

    public function delete()
    {
        $ret = parent::delete();
        $this->flushCache();
        return $ret;
    }

    public function insert($data, $is_mulit = false)
    {
        $ret = parent::insert($data, $is_mulit);
        $this->flushCache(':list');
        return $ret;
    }

    public function join($table, $first, $second = null, $type = 'inner')
    {
        $this->cache_tag[] = 'join:'.$table;
        return parent::join($table, $first, $second, $type);
    }

    private function getCacheKey($tag = '')
    {
        $table = $this->from;
        $hash =sha1($this->getSelectSql() . json_encode($this->build));
        return "DB:{$table}{$tag}:$hash";
    }

    private function flushCache($tag = '')
    {
        $table = $this->from;
        Cache::delRegex("*:{$table}{$tag}:*");
        Cache::flush('join:'.$table);
    }
}