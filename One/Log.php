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

        $str = $code . '|' . date('Y-m-d H:i:s') . '|' . self::$conf['id'] . '|' . $name . ':' . $line . '|' . $data . "\n";
        error_log($str, 3, $path);

    }
}