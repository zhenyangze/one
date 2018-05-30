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
        $this->_log($data, $k + 1, 3, $prefix);
    }

    /**
     * @param $data
     * @param int $k
     * @param $prefix
     */
    public function notice($data, $prefix = 'notice', $k = 0)
    {
        $this->_log($data, $k + 1, 2, $prefix);
    }

    /**
     * @param $data
     * @param int $k
     * @param $prefix
     */
    public function warn($data, $prefix = 'warn', $k = 0)
    {
        $this->_log($data, $k + 1, 1, $prefix);
    }

    /**
     * @param $data
     * @param int $k
     * @param $prefix
     */
    public function error($data, $prefix = 'error', $k = 0)
    {
        $this->_log($data, $k + 1, 0, $prefix);
    }


    private function _log($data, $k = 0, $code = 3, $prefix = 'vic')
    {

        $dir = self::$conf['path'] . '/' . date('Y-m-d') . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = $dir . $prefix . date('H') . '.log';
        if (is_string($data)) {
            $data = str_replace("\n", ' ', $data);
        } else {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 12);
        $name = $trace[$k]['file'];
        $line = $trace[$k]['line'];

        $code = $this->levels[$code];

        $str = $code . '|' . date('Y-m-d H:i:s') . '|' . \One\Facades\Request::id() . '|' . $name . ':' . $line . '|' . $data . "\n";
        error_log($str, 3, $path);

    }
}