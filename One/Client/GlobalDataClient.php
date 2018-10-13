<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/10/12
 * Time: 下午9:55
 */

namespace One\Client;


use One\Protocol\ProtocolAbstract;
use One\Swoole\GlobalData;

class GlobalDataClient
{
    /**
     * @var GlobalData
     */
    private $global;

    /**
     * @var \swoole_client
     */
    private $client = null;

    private $conf = [];

    /**
     * @var ProtocolAbstract
     */
    private $protocol;

    public function __construct($conf)
    {
        $this->global = new GlobalData();
        $this->conf = $conf;
        $this->protocol = new $conf['protocol'];
    }

    public function connect()
    {
        $this->client = new \swoole_client(SWOOLE_SOCK_TCP);
        $r = $this->client->connect($this->conf['ip'],$this->conf['port'],1);
        if($r){
            echo "global Data Server connect success\n";
        }else{
            echo "global Data Server connect fail\n";
            $this->client = null;
        }
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->global, $name)) {
            $data = json_encode(['m' => $name,'args' => $arguments]);
            $this->client->send($this->protocol->pack($data));
            if (strpos($name, 'get') !== false) {
                $this->client->recv();
            }
        }
    }
}