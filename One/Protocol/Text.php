<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/12
 * Time: 16:32
 */

namespace One\Protocol;


class Text extends ProtocolAbstract
{

    /**
     * 分包
     * @param $fd
     * @param $call
     */
    protected function unpack($fd, $call)
    {
        $len = strpos($this->data[$fd], "\n");
        if ($len === false) {
            return;
        }
        $data = substr($this->data[$fd], 0, $len);
        $this->data[$fd] = substr($this->data[$fd], $len + 1);
        $call($data);
        $this->unpack($fd, $call);
    }

    /**
     * 打包
     * @param string $buf
     * @return string
     */
    public function pack($buf)
    {
        return $buf . "\n";
    }

}
