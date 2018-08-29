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
class Protocol
{
    use ConfigTrait;

    protected $i = 0;

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

    public $worker_id = 0;
    public $worker_pid = 0;

    public function setPid($id, $pid)
    {
        $this->worker_id = $id;
        $this->worker_pid = $pid;
        $this->table()->set('worker_' . $id, ['id' => $pid]);
    }


    public $_fd_name = [];

    /**
     * 给fd绑定别名
     * @param $fd
     */
    public function bindName($fd, $name)
    {
        $this->_fd_name[$name][$fd] = 1;
    }


    public function unBindFd($fd)
    {
        foreach ($this->_fd_name as $name => $fds) {
            if (isset($fds[$fd])) {
                unset($this->_fd_name[$name][$fd]);
                if (count($this->_fd_name[$name]) == 0) {
                    unset($this->_fd_name[$name]);
                }
                return 1;
            }
        }
        return 0;
    }

    /**
     * @var \swoole_table
     */
    private static $_table = null;

    private static function createTable()
    {
        $table = new \swoole_table(1024);
        $table->column('id', \swoole_table::TYPE_INT, 4);       //1,2,4,8
        $table->create();
        self::$_table = $table;
    }

    /**
     * @return \swoole_table
     */
    public function table()
    {
        return self::$_table;
    }

    public function unBindName($name)
    {
        unset($this->_fd_name[$name]);
    }

    /**
     * @param $name
     * @return array
     */
    public function getFd($name)
    {
        if(isset($this->_fd_name[$name])){
            return array_keys($this->_fd_name[$name]);
        }else{
            return [];
        }
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

    private $worker_pids = [];


    public function getWorkerPids()
    {
        if (!$this->worker_pids) {
            if (isset(self::$_server->setting['worker_num'])) {
                $count = self::$_server->setting['worker_num'];
            } else {
                $count = swoole_cpu_num();
            }
            $pids = [];
            for ($i = 0; $i < $count; $i++) {
                $pid = $this->table()->get('worker_' . $i);
                if ($pid) {
                    $pids[] = $pid['id'];
                }
            }
            $this->worker_pids = $pids;
        }
        return $this->worker_pids;
    }

    public function sendToAll($params, $send_all = false)
    {
        if ($send_all) {
            Cache::set('signal', $params, 10);
            foreach ($this->getWorkerPids() as $pid) {
                if($pid != $this->worker_pid){
                    \swoole_process::kill($pid, SIGUSR1);
                }
            }
        }

        $name = array_shift($params);
        $send_type = array_pop($params);

        $fds = $this->getFd($name);
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

    private function sendInfoByName(...$params)
    {
        return $this->sendToAll($params, true);
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


    public static function runAll()
    {
        if (self::$_server === null) {
            self::_check();
            self::createTable();
            $server = self::startServer(array_shift(self::$conf));
            echo "start server\n";
            self::addServer($server);
            echo "run all\n";
            self::$_server = $server;
            $server->start();
        }
    }


    private static function addServer(\swoole_server $server)
    {
        foreach (self::$conf as $conf) {
            $port = $server->addListener($conf['ip'], $conf['port'], $conf['mode']);
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

        if (isset($conf['set'])) {
            $server->set($conf['set']);
        }

        self::onEvent($server, $conf['action'], $server, $conf, ['onClose', 'onWorkerStart']);

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

