<?php

namespace One;

abstract class Event
{
    /**
     * 获取访问的对象
     * @return mixed
     */
    abstract function getAccessor();

    /**
     * 设置事件
     * @return mixed
     */
    abstract function setEvents();

    private static $accessors = [];

    private $args = [];

    public function __construct()
    {
        $this->args = func_get_args();
    }

    protected function getObject()
    {
        $class = $this->getAccessor();
        if (is_object($class)) {
            return $class;
        } else if (!isset(self::$accessors[$class])) {
            self::$accessors[$class] = new $class(...$this->args);
        }
        return self::$accessors[$class];
    }

    public function __call($name, $arguments)
    {
        $m = ucwords($name);
        $before = 'before' . $m;
        $after = 'after' . $m;
        $events = $this->setEvents();
        $object = $this->getObject();
        if (isset($events[$before])) {
            if ($this->callBefore($events[$before], $object, $arguments) === false) {
                return false;
            }
        }
        $r = call_user_func_array([$object, $name], $arguments);
        if (isset($events[$after])) {
            $this->callAfter($events[$after], $object, $arguments, $r);
        }
        return $r;
    }

    private function callAfter($m, $obj, $args, &$r)
    {
        if (is_array($m)) {
            $m[0]->{$m[1]}($obj, $args, $r);
        } else {
            $m($obj, $args, $r);
        }
    }

    private function callBefore($m, $obj, &$args)
    {
        if (is_array($m)) {
            return $m[0]->{$m[1]}($obj, $args);
        } else {
            return $m($obj, $args);
        }
    }

}