<?php

namespace One\Database\Mysql;


/**
 * Class Model
 * @mixin CacheBuild
 * @method static transaction(\Closure $call)
 */
class Model extends ArrayModel
{
    use RelationTrait;

    protected $_connection = 'default';

    protected $_cache_time = 600;

    protected $_cache_column = [];

    CONST TABLE = '';

    private $_build = null;

    private function build()
    {
        if (!$this->_build) {
            $this->_build = new EventBuild($this->_connection, $this, get_called_class(), static::TABLE);
        }
        if ($this->_cache_time > 0) {
            $this->_build->cache($this->_cache_time);
        }
        if ($this->_cache_column) {
            $this->_build->cacheColumn($this->_cache_column);
        }
        return $this->_build;
    }

    public function __call($name, $arguments)
    {
        return $this->build()->$name(...$arguments);
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public function __get($name)
    {
        if (method_exists($this, $name)) {
            $this->$name = $this->$name()->setRelation()->get();
            return $this->$name;
        }
    }

    public function events()
    {
        return [];
    }

}
