<?php

namespace One\Cache;

use One\ConfigTrait;

/**
 * Class Redis
 * @package One\Cache
 * @mixin \Redis
 */
class Redis extends Cache
{
    use ConfigTrait;

    /**
     * @var \Redis
     */
    private $driver;

    public function __construct()
    {
        $this->connect();
    }

    public function getRedis()
    {
        return $this->driver;
    }

    public function __call($name, $arguments)
    {
        return $this->driver->$name(...$arguments);
    }

    private function connect()
    {
        $this->driver = new \Redis();
        $this->driver->connect(self::$conf['host'], self::$conf['port'], 0);
    }

    public function get($key, \Closure $closure = null, $ttl = null, $tags = [])
    {
        $val = $this->driver->get($this->getTagKey($key, $tags));
        if ((!$val) && $closure) {
            $val = $closure();
            $this->set($key, $val, $ttl, $tags);
        }else if($val){
            $val = igbinary_unserialize($val);
        }
        return $val;
    }

    public function del($key)
    {
        if(is_string($key)){
            $key = self::$conf['prefix'].$key;
        }
        $this->driver->del($key);
    }

    public function delRegex($key)
    {
        return $this->del($this->driver->keys($key));
    }

    public function flush($tag)
    {
        $id = md5(uuid());
        $this->set($tag, $id);
        return $id;
    }

    public function set($key, $val, $ttl = null, $tags = [])
    {
        $this->driver->set($this->getTagKey($key, $tags), igbinary_serialize($val), $ttl);
    }

}
