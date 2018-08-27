<?php

namespace One;

class Log
{
    use ConfigTrait;

    private $levels = [
        'ERROR', 'WARN', 'NOTICE', 'DEBUG'
    ];

    /**
     * @param $data
     * @param int $k
     * @param $prefix
     */
    public function debug($data, $prefix = 'debug', $k = 0)
    {
        $this->_log($data, $k + 2, 3, $prefix);
    }

    /**
     * @param $data
     * @param int $k
     * @param $prefix
     */
    public function notice($data, $prefix = 'notice', $k = 0)
    {
        $this->_log($data, $k + 2, 2, $prefix);
    }

    /**
     * @param $data
     * @param int $k
     * @param $prefix
     */
    public function warn($data, $prefix = 'warn', $k = 0)
    {
        $this->_log($data, $k + 2, 1, $prefix);
    }

    /**
     * @param $data
     * @param int $k
     * @param $prefix
     */
    public function error($data, $prefix = 'error', $k = 0)
    {
        $this->_log($data, $k + 2, 0, $prefix);
    }


    private function _log($data, $k = 0, $code = 3, $prefix = 'vic')
    {
        if (!is_dir(self::$conf['path'])) {
            mkdir(self::$conf['path'], 0755, true);
        }
        $path = self::$conf['path'] . '/' . $prefix . '-' . date('Y-m-d') . '.log';
        if (is_string($data)) {
            $data = str_replace("\n", ' ', $data);
        } else {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 16);
        $name = $trace[$k]['file'];
        $line = $trace[$k]['line'];

        $code = $this->levels[$code];


        $trace_id = $this->getTraceId();

        $str = $code . '|' . date('Y-m-d H:i:s') . '|' . $trace_id . '|' . $name . ':' . $line . '|' . $data . "\n";
        error_log($str, 3, $path);

    }

    private $_traceId = [];

    /**
     * 在协成环境统一TraceId
     * @param $id
     * @return string
     */
    public function bindTraceId($id)
    {
        $pid = \Swoole\Coroutine::getuid();
        if ($pid == -1) {
            $this->warn('bindTraceId false : ' . $id);
        }
        if (!isset($this->_traceId[$pid])) {
            $this->warn('bindTraceId get pid false : ' . $pid);
        }
        $this->_traceId[$id] = $this->_traceId[$pid];
        return $id;
    }

    /**
     * 请求完成刷新 清除已经关闭的id
     */
    public function flushTraceId()
    {
        $cids = \Swoole\Coroutine::listCoroutines();
        $t = [];
        foreach ($cids as $id) {
            if (isset($this->_traceId[$id])) {
                $t[$id] = $this->_traceId[$id];
            }
        }
        $this->_traceId = $t;
    }


    public function getTraceId()
    {
        $trace_id = self::$conf['id'];

        if (php_sapi_name() == 'cli' && class_exists('\Swoole\Coroutine')) {
            $cid = \Swoole\Coroutine::getuid();
            if ($cid > -1) {
                if (isset($this->_traceId[$cid])) {
                    $trace_id = $this->_traceId[$cid];
                } else {
                    //如果直接调用go创建协成这里获取不到id 所有创建协成请调用oneGo
                    $this->warn('get trace_id fail : ' . $cid);
                }
            }
        }

        return $trace_id;

    }
}