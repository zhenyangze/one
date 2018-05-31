<?php

namespace One\Cache;

use One\ConfigTrait;

class File extends Cache
{
    use ConfigTrait;

    private $child_dir = '';

    public function __construct($dir = '')
    {
        $this->child_dir = $dir;
        $this->mkdir();
    }

    private function mkdir()
    {
        if($this->child_dir){
            $dir = self::$conf['path'] . '/' . $this->child_dir;
        }else{
            $dir = self::$conf['path'];
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function get($key, \Closure $closure = null, $ttl = 0, $tags = [])
    {
        $k = $this->getTagKey($key, $tags);
        $f = $this->getFileName($k);
        if (file_exists($f)) {
            $str = file_get_contents($f);
            if ($str) {
                $time = substr($str, 0, 10);
                $str = substr($str, 10);
                if ($time > time()) {
                    return unserialize($str);
                }
            }
        }
        if ($closure) {
            $val = $closure();
            $this->set($key, $val, $ttl, $tags);
            return $val;
        } else {
            $this->del($key);
            return false;
        }
    }

    public function delRegex($key)
    {
        $this->del(glob($key));
    }

    public function flush($tag)
    {
        $id = md5(uuid());
        $this->set($tag, $id);
        return $id;
    }

    public function set($key, $val, $ttl = 0, $tags = [])
    {
        $key = $this->getTagKey($key, $tags);
        $file = $this->getFileName($key);
        file_put_contents($file, (time() + $ttl) . serialize($val));
    }

    public function del($key)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                @unlink($this->getFileName($k));
            }
        } else {
            return @unlink($this->getFileName(self::$conf['prefix'] . $key));
        }
    }

    private function getFileName($key)
    {
        if ($this->child_dir) {
            return self::$conf['path'] . '/' . $this->child_dir . '/' . $key;
        } else {
            return self::$conf['path'] . '/' . $key;
        }
    }

}