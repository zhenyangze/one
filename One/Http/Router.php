<?php

namespace One;

use One\Facades\Request as FacadeRequest;
use One\Facades\Cache;
use One\Http\Request;
use One\Http\Response;

class Router
{

    use ConfigTrait;

    public $info = [];

    public $as_info = [];

    public $args = [];

    public $paths = [];

    public $uri = '';

    public $class = '';

    public $method = '';

    private $httpRequest = null;

    private $httpResponse = null;

    public function __construct(Request $request, Response $response)
    {
        $this->httpRequest = $request;
        $this->httpResponse = $response;
    }


    public function loadRouter()
    {
        $key = md5(__FILE__ . filemtime(self::$conf['path']));

        $info = Cache::get($key, function () {
            require self::$conf['path'];
            return [$this->info, $this->as_info];
        }, 60 * 60 * 24 * 30);

        $this->info = $info[0];
        $this->as_info = $info[1];
    }

    /**
     * @return array
     */
    private function getPath()
    {
        $this->uri = FacadeRequest::uri();
        $this->paths = explode('/', $this->uri);
        return $this->paths;
    }

    /**
     * @return string
     */
    private function getKey()
    {
        $method = FacadeRequest::method();
        $paths = $this->getPath();
        foreach ($paths as $i => $v) {
            if (is_numeric($v)) {
                $paths[$i] = '#' . $v;
            }
        }
        $path = implode('.', $paths);
        if ($path === '' || $path === '.') {
            $path = '';
        }
        $path = trim($path, '.');
        $path = trim($method . '.' . $path, '.');
        return $path;
    }

    private function matchRouter($arr, $key)
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            foreach ($keys as $v) {
                $arr = $this->rules($arr, $v);
                if ($arr === null) {
                    return null;
                }
                if (is_string($arr) || (count($arr) == 1 && isset($arr[0]))) {
                    break;
                }
            }
            return $arr;
        } else {
            return $this->rules($arr, $key);
        }
    }


    private function rules($arr, $v)
    {
        if (isset($arr[$v])) {
            return $arr[$v];
        }
        $keys = array_keys($arr);
        foreach ($keys as $key) {
            $s = substr($key, 0, 1);
            if ($s == '{') {
                if (substr($v, 0, 1) == '#') {
                    $v = substr($v, 1);
                }
                if (substr($key, 1, 2) == 'id') {
                    if (is_numeric($v)) {
                        $this->args[] = $v;
                        return $arr[$key];
                    }
                } else {
                    $this->args[] = $v;
                    return $arr[$key];
                }
            } else if ($s == '`') {
                if (preg_match('/' . substr($key, 1, -1) . '/', $v)) {
                    $this->args[] = $v;
                    return $arr[$key];
                }
            }
        }
        return null;
    }

    private function getAction()
    {
        $info = $this->matchRouter($this->info, $this->getKey());
        if (!$info) {
            alert('Not Found', 404);
        }
        if (is_array($info)) {
            if (isset($info[0])) {
                $info = $info[0];
            } else {
                alert('Not Found', 404);
            }
        }
        $fm = [];
        if (is_array($info)) {
            $fm[] = $info;
            if (isset($info['middle'])) {
                foreach ($info['middle'] as $v) {
                    $fm[] = $v;
                }
            }
        } else {
            $fm[] = $info;
        }
        return $fm;
    }


    /**
     * 执行路由对应的方法
     */
    public function exec()
    {
        $fm = $this->getAction();
        $act = is_array($fm[0]) ? $fm[0]['use'] : $fm[0];
        list($this->class, $this->method) = explode('@', $act);
        $r = [];
        foreach ($fm as $i => $v) {
            if ($i > 0) {
                $r[] = function ($handler) use ($v) {
                    return function () use ($v, $handler) {
                        return call($v, array_merge([$handler], $this->args));
                    };
                };
            }
        }

        $action = function () use ($fm) {
            $cache = 0;
            if (is_array($fm[0])) {
                $ac = $fm[0]['use'];
                if (isset($fm[0]['cache'])) {
                    $cache = $fm[0]['cache'];
                    $key = md5($ac . ':' . implode(',', $this->args));
                    $res = Cache::get($key);
                    if ($res) {
                        return $res;
                    }
                }
            } else {
                $ac = $fm[0];
            }
            $res = call($ac, $this->args);
            if ($cache) {
                Cache::set($key, $res, $cache);
            }
            return $res;
        };

        $run = $this->runBox($action, $r);
        return $run();
    }


    private function runBox($handler, $box)
    {
        foreach ($box as $fn) {
            $handler = $fn($handler);
        }
        return $handler;
    }

    private $group_info = [];
    private $max_group_depth = 200;

    /**
     * @param array $rule ['prefix' => '','namespace'=>'','cache'=>1,'middle'=>[]]
     * @param \Closure $route
     */
    public function group($rule, $route)
    {
        $len = $this->max_group_depth - count($this->group_info);
        $this->group_info[$len] = $rule;
        ksort($this->group_info);
        $route();
        unset($this->group_info[$len]);
    }

    private function withGroupAction($group_info, $action)
    {
        if (is_array($action)) {
            if (isset($group_info['as']) && isset($action['as'])) {
                $action['as'] = trim($group_info['as'], '.') . '.' . $action['as'];
            }
            if (isset($group_info['namespace'])) {
                $action['use'] = '\\' . $group_info['namespace'] . '\\' . trim($action['use'], '\\');
            }
            if (isset($group_info['middle'])) {
                if (!isset($action['middle'])) {
                    $action['middle'] = [];
                }
                $action['middle'] = array_merge($action['middle'], array_reverse($group_info['middle']));
            }
            if (isset($group_info['cache'])) {
                $action['cache'] = $group_info['cache'];
            }
        } else {
            if (isset($group_info['namespace'])) {
                $action = '\\' . $group_info['namespace'] . '\\' . trim($action, '\\');
            }
            $action = ['use' => $action, 'middle' => []];
            if (isset($group_info['middle'])) {
                $action['middle'] = array_merge($action['middle'], array_reverse($group_info['middle']));
            }
            if (isset($group_info['cache'])) {
                $action['cache'] = $group_info['cache'];
            }
        }
        return $action;
    }

    private function withGroupPath($group_info, $path)
    {
        $path = '/' . trim($path, '/');
        if (isset($group_info['prefix'])) {
            $prefix = trim($group_info['prefix'], '/');
            $path = '/' . trim($prefix, '/') . $path;
        }
        return $path;
    }


    private function set($method, $path, $action)
    {
        foreach ($this->group_info as $value) {
            $action = $this->withGroupAction($value, $action);
            $path = $this->withGroupPath($value, $path);
        }
        if (is_array($action)) {
            $this->createAsInfo($path, $action);
        }
        $arr = explode('/', $method . $path);
        if (is_array($action)) {
            $v = end($arr);
            if ($v !== '') {
                $arr[] = '';
            }
        }
        $this->info = array_merge_recursive($this->info, $this->setPath($arr, $action));
    }


    /**
     * @param $path
     * @param array $action
     */
    private function createAsInfo($path, $action)
    {
        if (isset($action['as'])) {
            $this->as_info[$action['as']] = rtrim($path, '/');
        }
    }

    private function setPath($arr, $v, $i = 0)
    {
        if (isset($arr[$i])) {
            if (is_numeric($arr[$i])) {
                $arr[$i] = '#' . $arr[$i];
            } else if ($arr[$i] == '') {
                $arr[$i] = 0;
            }
            return [$arr[$i] => $this->setPath($arr, $v, $i + 1)];
        } else {
            return $v;
        }
    }

    /**
     * @param string $path
     * @param string $controller
     */
    public function controller($path, $controller)
    {
        $this->get($path, $controller . '@' . 'getAction');
        $this->post($path, $controller . '@' . 'postAction');
        $this->put($path, $controller . '@' . 'putAction');
        $this->delete($path, $controller . '@' . 'deleteAction');
        $this->patch($path, $controller . '@' . 'patchAction');
        $this->head($path, $controller . '@' . 'headAction');
        $this->options($path, $controller . '@' . 'optionsAction');
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public function shell($path, $action)
    {
        $this->set('shell', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public function get($path, $action)
    {
        $this->set('get', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public function post($path, $action)
    {
        $this->set('post', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public function put($path, $action)
    {
        $this->set('put', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public function delete($path, $action)
    {
        $this->set('delete', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public function patch($path, $action)
    {
        $this->set('patch', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public function head($path, $action)
    {
        $this->set('head', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public function options($path, $action)
    {
        $this->set('options', $path, $action);
    }


}

