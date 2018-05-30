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

    protected $connection = 'default';

    protected $cache_time = 600;

    CONST TABLE = '';

    private $build = null;

    private function build()
    {
        if (!$this->build) {
            $this->build = new EventBuild($this->connection, $this, get_called_class(), static::TABLE);
        }
        if ($this->cache_time > 0) {
            $this->build->cache($this->cache_time);
        }
        return $this->build;
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
