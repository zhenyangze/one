<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/12
 * Time: 16:51
 */

namespace One\Protocol;


abstract class ProtocolAbstract
{
    protected $data = [];

    protected function addData(&$buf, $fd)
    {
        if (isset($this->data[$fd])) {
            $this->data[$fd] .= $buf;
        }
    }

    /**
     * Decode.
     *
     * @param string $buffer
     * @param int $fd
     * @param \Closure $call
     * @return string
     */
    public function decode(&$buffer, $fd, $call)
    {
        $this->addData($buffer, $fd);
        $this->unpack($fd, $call);
    }

    /**
     * @param int $fd
     * @param \Closure $call
     * @return mixed
     */
    abstract protected function unpack($fd, $call);

    /**
     * @param string $buf
     * @return string
     */
    abstract protected function pack($buf);


}