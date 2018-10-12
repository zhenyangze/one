<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/12
 * Time: 16:32
 */

namespace One\Protocol;


class Frame extends ProtocolAbstract
{

    const HEAD_LEN = 4;

    /**
     * 分包
     * @param $fd
     * @param $call
     */
    protected function unpack($fd, $call)
    {
        $len = strlen($this->data[$fd]);
        if ($len <= self::HEAD_LEN) {
            return;
        }
        $p = unpack('Nlength', $this->data[$fd]);
        if ($len >= $p['length']) {
            $data = substr($this->data[$fd], 0, $p['length']);
            $this->data[$fd] = substr($this->data[$fd], $p['length']);
            $call(substr($data, self::HEAD_LEN, $p['length'] - self::HEAD_LEN));
            $this->unpack($fd, $call);
        }
    }

    /**
     * 打包
     * @param string $buf
     * @return string
     */
    public function pack($buf)
    {
        $len = self::HEAD_LEN + strlen($buf);
        return pack('N', $len) . $buf;
    }
}
