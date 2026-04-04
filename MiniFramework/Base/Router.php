<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2026 http://www.sunbloger.com
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
        $this->determineRouteType();
        $this->route(Config::getInstance()->load('route', false));
    }
    
    /**
     * 确定路由类型
     */
    private function determineRouteType()
    {
        if (isCli()) {
            // CLI (/index.php Controller/Action param1=value1 param2=value2 ...)
            $this->setRouteType('cli');
        } elseif (false === strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            // Rewrite (/Controller/Action/param1/value1/param2/value2)
            $this->setRouteType('rewrite');
        } else {
            // GET (/index.php?c=index&a=index)
            $this->setRouteType('get');
        }
    }

    /**
     * 路由
     * 
     * @param array $rules
     * @throws Exception
     */
    public function route($rules = null)
    {
        $routeInfo = $this->dispatchRoute($rules);
        $controller = $routeInfo['c'];
        $action = $routeInfo['a'];
        
        $this->validateRoute($controller, $action);

        return $routeInfo;
    }
    
    /**
     * 分发路由
     * 
     * @param array $rules
     * @return array
     * @throws Exception
     */
    private function dispatchRoute($rules = null)
    {
        if ($this->_routeType == 'cli') {
            return $this->handleCliRoute();
        }
        
        if (isset($rules) && is_array($rules)) {
            $customRoute = $this->customRoute($rules);
            if ($customRoute !== false) {
                $this->setRouteType('custom');
                return $customRoute;
            }
        }
        
        if ($this->_routeType == 'rewrite') {
            return $this->handleRewriteRoute();
        } elseif ($this->_routeType == 'get') {
            return $this->handleGetRoute();
        }
        
        return ['c' => 'index', 'a' => 'index'];
    }
    
    /**
     * 处理CLI路由
     * 
     * @return array
     * @throws Exception
     */
    private function handleCliRoute()
    {
        if (!isset($_SERVER['argc']) || $_SERVER['argc'] <= 1) {
            throw new Exception('When running in console mode, the controller and action must be specified.');
        }
        
        $m = [];
        if (preg_match("/^([a-zA-Z][a-zA-Z0-9]*)\/([a-zA-Z][a-zA-Z0-9]*)$/", $_SERVER['argv'][1], $m)) {
            return [
                'c' => isset($m[1]) ? $m[1] : 'index',
                'a' => isset($m[2]) ? $m[2] : 'index'
            ];
        } else {
            throw new Exception('The controller or action format is invalid. The correct format is controller/action.');
        }
    }
    
    /**
     * 处理Rewrite路由
     * 
     * @return array
     */
    private function handleRewriteRoute()
    {
        $this->_uriArray = $this->parseUrlToArray();
        return [
            'c' => (isset($this->_uriArray[1]) && !empty($this->_uriArray[1])) ? $this->_uriArray[1] : 'index',
            'a' => (isset($this->_uriArray[2]) && !empty($this->_uriArray[2])) ? $this->_uriArray[2] : 'index'
        ];
    }
    
    /**
     * 处理GET路由
     * 
     * @return array
     */
    private function handleGetRoute()
    {
        if (empty($_SERVER['QUERY_STRING'])) {
            return ['c' => 'index', 'a' => 'index'];
        }
        
        $queryStringArray = $this->_request->getQueryStringArray();
        return [
            'c' => isset($queryStringArray['c']) ? $queryStringArray['c'] : 'index',
            'a' => isset($queryStringArray['a']) ? $queryStringArray['a'] : 'index'
        ];
    }
    
    /**
     * 验证路由参数
     * 
     * @param string $controller
     * @param string $action
     * @throws Exception
     */
    private function validateRoute($controller, $action)
    {
        if (! $this->checkRoute($controller)) {
            throw new Exception('Controller name "' . $controller . '" invalid.', 404);
        }
        if (! $this->checkRoute($action)) {
            throw new Exception('Action name "' . $action . '" invalid.', 404);
        }
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
            $pattern = $rule;
            
            // 处理路由参数
            $pattern = preg_replace_callback('/<(.*?)>/', function($matches) use (&$names) {
                $param = $matches[1];
                if (strpos($param, ':')) {
                    list($name, $regex) = explode(':', $param, 2);
                    $names[] = $name;
                    return '(' . $regex . ')';
                } else {
                    $names[] = $param;
                    return '([^/]+)';
                }
            }, $pattern);
            
            // 构建正则表达式
            $regex = '/^\/' . str_replace('/', '\/', $pattern) . '$/';
            $matches = [];
            
            if (preg_match_all($regex, $_SERVER['REQUEST_URI'], $matches)) {
                // 处理捕获的参数
                if (!empty($names)) {
                    array_shift($matches); // 移除完整匹配
                    $params = Params::getInstance();
                    foreach ($names as $index => $name) {
                        if (isset($matches[$index][0])) {
                            $params->setParam($name, $matches[$index][0]);
                        }
                    }
                }
                
                // 解析目标控制器和动作
                if (strpos($target, '/')) {
                    list($controller, $action) = explode('/', $target, 2);
                    return ['c' => $controller, 'a' => $action];
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
        
        if ($requestUri !== $baseUrl) {
            $requestUri = str_replace($baseUrl, '', $requestUri);
        }
        
        return explode('/', $requestUri);
    }

    /**
     * 检查路由参数合法性
     *
     * @param mixed $value            
     * @return bool
     */
    protected function checkRoute($value)
    {
        if (!is_string($value)) {
            return false;
        }
        return (bool) preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $value);
    }

    /**
     * 判断PHP是否处于CLI模式下运行
     *
     * @return boolean
     */
    public function isCli()
    {
        return isCli();
    }
}
