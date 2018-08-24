<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2018/8/24
 * Time: 下午3:21
 */

namespace One\Swoole;


class Protocol
{
    protected static $conf = [];

    protected $i = 0;

    const SWOOLE_SERVER = 0;
    const SWOOLE_HTTP_SERVER = 1;
    const SWOOLE_WEBSOCKET_SERVER = 2;

    private static $_server = null;


    public function __construct($server_type = self::SWOOLE_HTTP_SERVER)
    {
        $this->i = count(self::$conf);
        $this->addConf('server_type', $server_type);
        $this->ip()->port()->mode()->sockType();
    }

    /**
     * 返回全局server
     * @return \swoole_websocket_server
     */
    public static function getServer()
    {
        return self::$_server;
    }


    /**
     * 设置端口
     * @param int $port
     * @return Protocol
     */
    public function port($port = '9501')
    {
        return $this->addConf(__FUNCTION__, $port);
    }

    /**
     * 运行的模式
     * @param int $mode
     * @return Protocol
     */
    public function mode($mode = SWOOLE_PROCESS)
    {
        return $this->addConf(__FUNCTION__, $mode);
    }

    /**
     * 指定Socket的类型
     * @param int $type
     * @return Protocol
     */
    public function sockType($type = SWOOLE_SOCK_TCP)
    {
        return $this->addConf(__FUNCTION__, $type);
    }

    /**
     * 设置swoole_server运行时的各项参数
     * @param array $config
     * @return Protocol
     */
    public function set(array $config)
    {
        return $this->addConf(__FUNCTION__, $config);
    }

    /**
     * 设置ip
     * @param string $ip
     * @return Protocol
     */
    public function ip($ip = '0.0.0.0')
    {
        return $this->addConf(__FUNCTION__, $ip);
    }

    /**
     * 当前协议运行的类
     * @param string $action
     * @return Protocol
     */
    public function action($action)
    {
        return $this->addConf(__FUNCTION__, $action);
    }

    public static function runAll()
    {

        self::_check();
        $server = self::startServer(array_shift(self::$conf));
        echo "start server\n";
        self::addServer($server);
        echo "run all\n";
        self::$_server = $server;
        $server->start();
    }


    private static function addServer(\swoole_server $server)
    {
        foreach (self::$conf as $conf) {
            $port = $server->addListener($conf['ip'], $conf['port'], $conf['mode']);
            if (isset($conf['set'])) {
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
                $server = new \swoole_websocket_server($conf['ip'], $conf['port'], $conf['mode'], $conf['sockType']);
                break;
            case self::SWOOLE_HTTP_SERVER:
                break;

            case self::SWOOLE_SERVER:
                break;

            default:
                echo "未知的服务类型\n";
                exit;
        }

        if (isset($conf['set'])) {
            $server->set($conf['set']);
        }

        self::onEvent($server, $conf['action'], $server, $conf);

        return $server;
    }


    private static function onEvent($self, $class, $server, $conf)
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
                    $self->on(substr($func->name, 2), [$obj, $func->name]);
                }
            }
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

        usort(self::$conf, function ($a, $b) {
            return $a['server_type'] > $b['server_type'] ? -1 : 1;
        });
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

    private function addConf($key, $val)
    {
        self::$conf[$this->i][$key] = $val;
        return $this;
    }
}

