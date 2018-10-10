<?php

namespace One\Facades;


/**
 * Class Cache
 * @package Facades
 * @mixin \One\Cache\Redis
 * @mixin \Redis
 * @method string get($key, \Closure $closure = null, $ttl = 0, $tags = []) static
 * @method bool delRegex($key) static
 * @method bool flush($tag) static
 * @method bool set($key, $val, $ttl = 0, $tags = []) static
 */
class Cache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return '\\One\\Cache\\'.config('cache.drive');
    }
}
