<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2024 http://www.sunbloger.com
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

class App
{

    /**
     * 控制器
     *
     * @var string
     */
    public $controller;

    /**
     * 动作
     *
     * @var string
     */
    public $action;

    /**
     * Router实例
     *
     * @var Router
     */
    protected $_router;

    /**
     * Params实例
     *
     * @var Params
     */
    protected $_params;

    /**
     * Request实例
     *
     * @var Request
     */
    protected $_request;

    /**
     * App实例
     *
     * @var App
     */
    protected static $_instance;

    /**
     * 数据库对象池
     *
     * @var array
     */
    private $_dbPool;

    /**
     * 获取实例
     *
     * @return object
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 构造
     */
    final protected function __construct()
    {
        set_error_handler('Mini\Base\Exception::customError');
        set_exception_handler('Mini\Base\Exception::customExcepion');
        
        if (LOG_ON === true) {
            Log::getInstance();
        }
    }

    /**
     * 克隆
     */
    private function __clone()
    {}

    /**
     * 开始
     */
    public function run()
    {
        $this->_params = Params::getInstance();
        $this->_request = Request::getInstance();
        $this->getRouter();
        $requestParams = $this->_request->parseRequestParams($this->_router->getRouteType());
        $isCli = $this->_router->isCli();
        unset($this->_router);
        if (! empty($requestParams)) {
            $this->_params->setParams($requestParams);
        }
        
        // include global function file.
        include (MINI_PATH . DS . 'Function' . DS . 'Global.func.php');
        
        // Check CSRF-Token
        if (CSRF_TOKEN_ON === true) {
            if ($isCli === false) {
                $serverCsrfToken = $this->_request->loadCsrfToken('session');
                if (! $serverCsrfToken) {
                    $this->_request->createCsrfToken(CSRF_TYPE);
                } else {
                    if ($this->_request->checkCsrfToken() === true) {
                        $this->_request->createCsrfToken(CSRF_TYPE);
                    } else {
                        $http = Http::getInstance();
                        $http->sendHttpStatus(403);
                        echo 'Client CSRF-Token invalid.';
                        die();
                    }
                }
            }
        }
        
        if (DB_AUTO_CONNECT === true) {
            $this->initDbPool();
        }
        
        $this->dispatch();
    }

    /**
     * 设置控制器
     * 
     * @param string $value
     * @return \Mini\Base\App
     */
    public function setController($value)
    {
        $this->controller = $value;
        
        return $this;
    }

    /**
     * 设置动作
     * 
     * @param string $value
     * @return \Mini\Base\App
     */
    public function setAction($value)
    {
        $this->action = $value;
        
        return $this;
    }

    /**
     * 调派
     */
    public function dispatch()
    {
        $controllerName = ucfirst($this->controller);
        $isApi = (REST_ON === true && $controllerName == 'Api') ? true : false;
        
        if ($isApi === true) {
            
            $apiName = ucfirst($this->action);
            $headers = $this->_request->getHeaders();
            if (isset($headers['Ver']) && preg_match("/^\d+$/", $headers['Ver'])) {
                $apiName .= '_V' . $headers['Ver'];
            }
            $apiFile = APP_PATH . DS . 'Api' . DS . $apiName . '.php';
            
            if (! file_exists($apiFile)) {
                throw new Exception('Api file "' . $apiFile . '" not found.', 404);
            }
            
            $apiName = APP_NAMESPACE . '\\Api\\' . $apiName;
            
            if ('Mini\\Base\\Rest' !== get_parent_class($apiName)) {
                throw new Exception('Api "' . $apiName . '" not extends "Rest" class.');
            }
            
            if (class_exists($apiName)) {
                $api = new $apiName();
            } else {
                throw new Exception('Api "' . $apiName . '" does not exist.', 404);
            }
        } else {
            $controllerFile = APP_PATH . DS . 'Controller' . DS . $controllerName . '.php';
            
            if (! file_exists($controllerFile)) {
                throw new Exception('Controller file "' . $controllerFile . '" not found.', 404);
            }
            
            $controllerName = APP_NAMESPACE . '\\Controller\\' . $controllerName;
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
            } else {
                throw new Exception('Controller "' . $controllerName . '" does not exist.', 404);
            }
            
            $action = $this->action . 'Action';
            
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                throw new Exception('Action "' . $this->action . '" does not exist.', 404);
            }
        }
    }

    /**
     * 获取路由器对象
     *
     * @return object
     */
    public function getRouter()
    {
        if ($this->_router === null) {
            $this->_router = new Router();
        }
        return $this->_router;
    }

    /**
     * 初始化数据库对象池
     *
     * @throws Exception
     * @return boolean
     */
    private function initDbPool()
    {
        $dbConfig = Config::getInstance()->load('database');
        if (is_array($dbConfig)) {
            foreach ($dbConfig as $dbKey => $dbParams) {
                $this->_dbPool[$dbKey] = \Mini\Db\Db::factory('Mysql', $dbParams);
            }
        } else {
            throw new Exception('Config "database" invalid.');
        }
        
        return true;
    }

    /**
     * 获取数据库对象池
     *
     * @return Object | NULL
     */
    public function getDbPool()
    {
        if (! isset($this->_dbPool)) {
            return null;
        }
        
        return $this->_dbPool;
    }
}
