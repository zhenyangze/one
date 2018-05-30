<?php

/**
 * @param $msg
 * @param $code
 */
function alert($msg, $code)
{
    throw new \One\Exceptions\HttpException($msg,$code);
}

/**
 * @param $path
 * @return mixed|null
 */
function config($path)
{
    static $config = null;
    $res = array_get($config, $path);
    if (!$res) {
        $p = strpos($path, '.');
        if ($p !== false) {
            $name = substr($path, 0, $p);
            $config[$name] = require(_APP_PATH_ . '/Config/' . $name . '.php');
        } else {
            $config[$path] = require(_APP_PATH_ . '/Config/' . $path . '.php');
        }
        $res = array_get($config, $path);
    }
    return $res;
}

/**
 * @param string $fn
 * @param array $args
 * @return mixed
 */
function call($fn, $args)
{
    if (strpos($fn, '@') !== false) {
        $cl = explode('@', $fn);
        return call_user_func_array([new $cl[0], $cl[1]], $args);
    } else {
        return call_user_func_array($fn, $args);
    }
}


/**
 * @param array $arr
 * @param $key
 * @return mixed|null
 */
function array_get($arr, $key)
{
    if (isset($arr[$key])) {
        return $arr[$key];
    } else if (strpos($key, '.') !== false) {
        $keys = explode('.', $key);
        foreach ($keys as $v) {
            if (isset($arr[$v])) {
                $arr = $arr[$v];
            } else {
                return null;
            }
        }
        return $arr;
    } else {
        return null;
    }
}


/**
 * @param array $arr
 * @param array $keys
 * @return mixed|null
 */
function array_get_not_null($arr, $keys)
{
    foreach ($keys as $v) {
        if (array_get($arr, $v) !== null) {
            return array_get($arr, $v);
        }
    }
    return null;
}

/**
 * uuid生成 php7+
 * @param string $prefix
 * @return string
 */
function uuid($prefix = '')
{
    $str = uniqid('', true);
    $arr = explode('.', $str);
    $str = $prefix . base_convert($arr[0], 16, 36) . base_convert($arr[1], 10, 36) . base_convert(bin2hex(random_bytes(5)), 16, 36);
    $len = 24;
    $str = substr($str, 0, $len);
    if (strlen($str) < $len) {
        $mt = base_convert(bin2hex(random_bytes(5)), 16, 36);
        $str = $str . substr($mt, 0, $len - strlen($str));
    }
    return $str;
}


/**
 * @param $str
 * @param null $allow_tags
 * @return string
 */
function filterXss($str, $allow_tags = null)
{
    $str = strip_tags($str, $allow_tags);
    if ($allow_tags !== null) {
        while (true) {
            $l = strlen($str);
            $str = preg_replace('/(<[^>]+?)([\'\"\s]+on[a-z]+)([^<>]+>)/i', '$1$3', $str);
            $str = preg_replace('/(<[^>]+?)(javascript\:)([^<>]+>)/i', '$1$3', $str);
            if (strlen($str) == $l) {
                break;
            }
        }
    }
    return $str;
}
