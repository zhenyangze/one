<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 下午3:21
 */

namespace One\Swoole;


use One\ConfigTrait;
use One\Facades\Cache;

/**
 * Class Protocol
 * @mixin \swoole_websocket_server
 * @package One\Swoole
 */
class OneServer
{
    use ConfigTrait;

    public $worker_id = 0;
    public $worker_pid = 0;
    public $is_task = false;


    const SWOOLE_SERVER = 0;
    const SWOOLE_HTTP_SERVER = 1;
    const SWOOLE_WEBSOCKET_SERVER = 2;

    /**
     * @var \swoole_websocket_server
     */
    private static $_server = null;

    private static $_pro = null;


    private function __construct()
    {

    }

    private function __clone()
    {

    }


    public function __call($name, $arguments)
    {
        if (method_exists(self::$_server, $name)) {
            self::$_server->$name(...$arguments);
        } else {
            throw new \Exception('方法不存在:' . $name);
        }
    }

    /**
     * @return GlobalData
     */
    public function globalData()
    {
        static $g = null;
        if($g === null){
            $g = new GlobalData();
        }
    }

    /**
     * 给fd绑定别名
     * @param $fd
     */
    public function bindName($fd, $name)
    {
        $this->globalData()->bindName($fd, $name);
    }

    /**
     * 解除绑定
     * @param $fd
     */
    public function unBindFd($fd)
    {
        $this->globalData()->unBindFd($fd);
    }

    /**
     * 解除绑定
     * @param $name
     */

    public function unBindName($name)
    {
        $this->globalData()->unBindName($name);
    }

    /**
     * @param $name
     * @return array
     */
    public function getFdByName($name)
    {
        return $this->globalData()->getFdByName($name);
    }


    public function sendToByName($name, $port, $data, $server_socket = -1)
    {
        return $this->sendInfoByName($name, $port, $data, $server_socket, 'sendwait');
    }

    public function sendFileByName($name, $filename, $offset = 0, $length = 0)
    {
        return $this->sendInfoByName($name, $filename, $offset, $length, 'sendwait');
    }

    public function sendWaitByName($name, $data)
    {
        return $this->sendInfoByName($name, $data, 'sendwait');
    }

    public function sendByName($name, $data)
    {
        return $this->sendInfoByName($name, $data, 'send');
    }

    public function pushByName($name, $data)
    {
        return $this->sendInfoByName($name, $data, 'push');
    }

    private function sendInfoByName(...$params)
    {
        $name = array_shift($params);
        $send_type = array_pop($params);
        $fds = $this->getFdByName($name);
        if ($fds) {
            foreach ($fds as $fd) {
                if (self::$_server->exist($fd)) {
                    return self::$_server->$send_type($fd, ...$params);
                }
            }
        } else {
            return false;
        }
    }


    /**
     * 返回全局server
     * @return $this
     */
    public static function getServer()
    {
        if (self::$_pro === null) {
            self::$_pro = new self();
        }
        return self::$_pro;
    }


    private static function e($str)
    {
        echo $str . "\n";
    }

    public static function runAll()
    {
        if (self::$_server === null) {
            self::_check();
            self::createTable();
            $server = self::startServer(self::$conf['server']);
            self::addServer($server);
            self::$_server = $server;
            self::e('server start');
            $server->start();
        }
    }


    private static function addServer(\swoole_server $server)
    {
        if (!isset(self::$conf['add_listener'])) {
            return false;
        }
        foreach (self::$conf['add_listener'] as $conf) {
            $port = $server->addListener($conf['ip'], $conf['port'], $conf['mode']);
            self::e("addListener {$conf['ip']} {$conf['port']}");
            if (isset($conf['set']) && $conf['set']) {
                $port->set($conf['set']);
            }
            self::onEvent($port, $conf['action'], $server, $conf);
        }
    }


    private static function startServer($conf)
    {
        $server = null;
        switch ($conf['server_type']) {
            case self::SWOOLE_WEBSOCKET_SERVER:
                $server = new \swoole_websocket_server($conf['ip'], $conf['port'], $conf['mode'], $conf['sock_type']);
                break;
            case self::SWOOLE_HTTP_SERVER:
                $server = new \swoole_http_server($conf['ip'], $conf['port'], $conf['mode'], $conf['sock_type']);
                break;
            case self::SWOOLE_SERVER:
                $server = new \swoole_server($conf['ip'], $conf['port'], $conf['mode'], $conf['sock_type']);
                break;
            default:
                echo "未知的服务类型\n";
                exit;
        }
        $_server_name = [
            self::SWOOLE_WEBSOCKET_SERVER => 'swoole_websocket_server',
            self::SWOOLE_HTTP_SERVER => 'swoole_http_server',
            self::SWOOLE_SERVER => 'swoole_server',
        ];

        self::e("server {$_server_name[$conf['server_type']]} {$conf['ip']} {$conf['port']}");

        if (isset($conf['set'])) {
            $server->set($conf['set']);
        }

        $e = ['onClose', 'onWorkerStart'];
        if($conf['server_type'] == self::SWOOLE_SERVER){
            $e[] = '__receive';
        }

        self::onEvent($server, $conf['action'], $server, $conf, $e);

        return $server;
    }


    private static function onEvent($sev, $class, $server, $conf, $call = [])
    {
        $base = [
            Server::class => 1,
            HttpServer::class => 1,
            WebSocket::class => 1
        ];

        $rf = new \ReflectionClass($class);
        $funcs = $rf->getMethods(\ReflectionMethod::IS_PUBLIC);
        $obj = new $class($server, $conf);

        foreach ($funcs as $func) {
            if (!isset($base[$func->class])) {
                if (substr($func->name, 0, 2) == 'on') {
                    $call[] = $func->name;
                }
            }
        }

        $call = array_unique($call);

        foreach ($call as $f) {
            $sev->on(substr($f, 2), [$obj, $f]);
        }

    }


    private static function _check()
    {

        $l = count(self::$conf);
        self::$conf = setArrkey(self::$conf, 'port');

        if (count(self::$conf) != $l) {
            echo "配置服务信息错误: 端口重复\n";
            exit;
        }

        if (count(self::$conf) == 0) {
            echo "请配置服务信息\n";
            exit;
        }

        foreach (self::$conf as $c) {
            if (!isset($c['action'])) {
                echo "配置服务信息错误: 缺少action\n";
                exit;
            }
        }
    }
}

