<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://www.sunbloger.com
// +---------------------------------------------------------------------------
// | Licensed under the Apache License, Version 2.0 (the "License");
// | you may not use this file except in compliance with the License.
// | You may obtain a copy of the License at
// |
// | http://www.apache.org/licenses/LICENSE-2.0
// |
// | Unless required by applicable law or agreed to in writing, software
// | distributed under the License is distributed on an "AS IS" BASIS,
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// | See the License for the specific language governing permissions and
// | limitations under the License.
// +---------------------------------------------------------------------------
// | Source: https://github.com/jasonweicn/miniframework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------
namespace Mini\Base;

class Router
{

    /**
     * Request实例
     *
     * @var Request
     */
    private $_request;

    /**
     * 路由方式
     *
     * @var string
     */
    protected $_routeType;

    /**
     * 路径数组
     *
     * @var string
     */
    protected $_uriArray;

    /**
     * 命令行参数数组
     *
     * @var array
     */
    protected $_cliParamsArray = [];

    /**
     * 构造
     */
    public function __construct()
    {
        $this->_request = Request::getInstance();
        
        if (true === $this->isCli()) {
            // CLI (/index.php Controller/Action param1=value1 param2=value2 ...)
            $this->setRouteType('cli');
        } elseif (false === strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            // Rewrite (/Controller/Action/param1/value1/param2/value2)
            $this->setRouteType('rewrite');
        } else {
            // GET (/index.php?c=index&a=index)
            $this->setRouteType('get');
        }
        $this->route(Config::getInstance()->load('route', false));
    }

    /**
     * 路由
     * 
     * @param array $rules
     * @throws Exception
     */
    public function route($rules = null)
    {
        $controller = $action = '';
        if ($this->_routeType == 'cli') {
            if (isset($_SERVER['argc']) && $_SERVER['argc'] > 1) {
                $m = [];
                if (preg_match("/^([a-zA-Z][a-zA-Z0-9]*)\/([a-zA-Z][a-zA-Z0-9]*)$/", $_SERVER['argv'][1], $m)) {
                    $controller = isset($m[1]) ? $m[1] : 'index';
                    $action = isset($m[2]) ? $m[2] : 'index';
                } else {
                    throw new Exception('The controller or action format is invalid. The correct format is controller/action.');
                }
            } else {
                throw new Exception('When running in console mode, the controller and action must be specified.');
            }
        } else {
            if (isset($rules) && is_array($rules)) {
                $result = $this->customRoute($rules);
                if ($result !== false) {
                    $controller = $result['c'];
                    $action = $result['a'];
                    $this->setRouteType('custom');
                }
            }
            if ($this->_routeType == 'rewrite') {
                $this->_uriArray = $this->parseUrlToArray();
                $controller = (isset($this->_uriArray[1]) && ! empty($this->_uriArray[1])) ? $this->_uriArray[1] : 'index';
                $action = (isset($this->_uriArray[2]) && ! empty($this->_uriArray[2])) ? $this->_uriArray[2] : 'index';
            } elseif ($this->_routeType == 'get') {
                if (empty($_SERVER['QUERY_STRING'])) {
                    $controller = $action = 'index';
                } else {
                    $queryStringArray = $this->_request->getQueryStringArray();
                    $controller = isset($queryStringArray['c']) ? $queryStringArray['c'] : 'index';
                    $action = isset($queryStringArray['a']) ? $queryStringArray['a'] : 'index';
                }
            }
        }
        
        if (! $this->checkRoute($controller)) {
            throw new Exception('Controller name "' . $controller . '" invalid.', 404);
        }
        if (! $this->checkRoute($action)) {
            throw new Exception('Action name "' . $action . '" invalid.', 404);
        }

        return ['c' => $controller, 'a' => $action];
    }

    /**
     * 自定义路由
     * 
     * @param array $rules
     * @return array|boolean
     */
    private function customRoute($rules)
    {
        foreach ($rules as $rule => $target) {
            $names = [];
            $matches = [];
            preg_match_all('/<(.*?)>/', $rule, $matches);
            if (! empty($matches[1])) {
                foreach ($matches[1] as $v) {
                    if (strpos($v, ':')) {
                        $names[] = $name = substr($v, 0, strpos($v, ':') - strlen($v));
                        $rule = preg_replace('/<' . $name . ':(.*?)>/', '($1)', $rule);
                    } else {
                        $rule = preg_replace('/<(.*?)>/', '($1)', $rule);
                    }
                }
            }
            $rule = '/^\/' . str_replace('/', '\/', $rule)  .'$/';
            $m = [];
            preg_match_all($rule, $_SERVER['REQUEST_URI'], $m);
            if (! empty($m[0])) {
                if (! empty($names)) {
                    unset($m[0]);
                    $m = array_values($m);
                    $p = Params::getInstance();
                    foreach ($names as $k => $name) {
                        if (isset($m[$k][0])) {
                            $p->setParam($name, $m[$k][0]);
                        }
                    }
                }
                if (strpos($target, '/')) {
                    $result['c'] = substr($target, 0, strpos($target, '/') - strlen($target));
                    $result['a'] = substr($target, strpos($target, '/') + 1, strlen($target));
                    return $result;
                }
            }
        }
        
        return false;
    }

    /**
     * 存入路由方式
     */
    public function setRouteType($type)
    {
        if ($type == 'cli' || $type == 'rewrite' || $type == 'get' || $type == 'custom') {
            $this->_routeType = $type;
        } else {
            throw new Exception('Router type invalid.', 500);
        }
        
        return $this;
    }

    /**
     * 读取路由方式
     *
     * @return string
     */
    public function getRouteType()
    {
        return $this->_routeType;
    }

    /**
     * 解析Url为数组
     *
     * @return array
     */
    private function parseUrlToArray()
    {
        $requestUri = $this->_request->getRequestUri();
        $baseUrl = $this->_request->getBaseUrl();
        if ($requestUri != $baseUrl) {
            $requestUri = str_replace($baseUrl, '', $requestUri);
        }
        $uriArray = explode('/', $requestUri);
        
        return $uriArray;
    }

    /**
     * 检查路由参数合法性
     *
     * @param mixed $value            
     * @return int
     */
    protected function checkRoute($value)
    {
        return preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $value);
    }

    /**
     * 判断PHP是否处于CLI模式下运行
     *
     * @return boolean
     */
    public function isCli()
    {
        return preg_match("/cli/i", PHP_SAPI) ? true : false;
    }
}
