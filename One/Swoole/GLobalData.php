<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/12
 * Time: 11:21
 */

namespace One\Swoole;

class GlobalData
{
    private $data = [];

    /**
     * 设置
     * @param string $key
     * @param mixed $val
     * @return int
     */
    public function set($key, $val)
    {
        $ar = $this->toKeys($key);
        $br = $ar;
        $wr = &$this->data;
        $len = count($ar);
        foreach ($ar as $i => $v) {
            array_shift($br);
            if (is_array($wr) && isset($wr[$v]) && ($i < $len - 1 && is_array($wr[$v]))) {
                $wr = &$wr[$v];
            } else {
                if ($v) {
                    $wr[$v] = $this->join($br, $val);
                } else {
                    $wr[] = $this->join($br, $val);
                }
                return 1;
            }
        }
        if ($wr !== $val) {
            $wr = $val;
        }
        return 1;
    }

    private function join($arr, $v, $i = 0)
    {
        if (isset($arr[$i])) {
            if ($arr[$i]) {
                return [$arr[$i] => $this->join($arr, $v, $i + 1)];
            } else {
                return [$this->join($arr, $v, $i + 1)];
            }
        } else {
            return $v;
        }
    }

    private function toKeys($key)
    {
        return explode('.', $key);
    }


    /**
     * 获取
     * @param string $key
     * @return array|mixed|null
     */
    public function get($key)
    {
        $ar = $this->toKeys($key);
        $wr = &$this->data;
        foreach ($ar as $v) {
            if (is_array($wr) && isset($wr[$v])) {
                $wr = &$wr[$v];
            } else {
                return null;
            }
        }
        return $wr;
    }

    /**
     * 删除
     * @param string $key
     * @return int
     */
    public function del($key)
    {
        $ar = $this->toKeys($key);
        $this->_del($ar);
        return 1;
    }

    private function _del($ar)
    {
        $k = array_pop($ar);
        $wr = &$this->data;
        foreach ($ar as $v) {
            if (is_array($wr) && isset($wr[$v])) {
                $wr = &$wr[$v];
            }
        }
        if (is_array($wr) && isset($wr[$k]) && (is_string($wr[$k]) || (is_array($wr[$k]) && count($wr[$k]) < 1))) {
            unset($wr[$k]);
        }

        if (count($ar) > 0) {
            $this->_del($ar);
        }
    }


    /**
     * @param $fd
     * @param $name
     * @param string $fd_key
     * @param string $name_key
     */
    public function bindName($fd, $name, $fd_key = 'fd', $name_key = 'name')
    {
        $this->set("{$name_key}-{$fd_key}.{$name}.{$fd}", time());
        $this->set("{$fd_key}-{$name_key}.{$fd}", $name);
    }

    /**
     * @param $fd
     * @param string $fd_key
     * @param string $name_key
     */
    public function unBindFd($fd, $fd_key = 'fd', $name_key = 'name')
    {
        $name = $this->getNameByFd($fd, $fd_key, $name_key);
        $this->del("{$name_key}-{$fd_key}.{$name}.{$fd}");
        $this->del("{$fd_key}-{$name_key}.{$fd}");
    }

    /**
     * 解除绑定
     * @param $name
     * @param string $fd_key
     * @param string $name_key
     */
    public function unBindName($name, $fd_key = 'fd', $name_key = 'name')
    {
        $fds = $this->get("{$name_key}-{$fd_key}.{$name}");
        foreach ($fds as $fd => $v) {
            $this->del("{$fd_key}-{$name_key}.{$fd}");
        }
        $this->del("{$name_key}-{$fd_key}.{$name}");
    }

    /**
     * @param $name
     * @param string $fd_key
     * @param string $name_key
     * @return array
     */
    public function getFdByName($name, $fd_key = 'fd', $name_key = 'name')
    {
        $arr = $this->get("{$name_key}-{$fd_key}.{$name}");
        return $arr ? $arr : [];
    }


    /**
     * @param $fd
     * @param string $fd_key
     * @param string $name_key
     * @return string
     */
    public function getNameByFd($fd, $fd_key = 'fd', $name_key = 'name')
    {
        return $this->get("{$fd_key}-{$name_key}.{$fd}");
    }

}
