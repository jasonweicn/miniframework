<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2017 http://www.sunbloger.com
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
// | Source: https://github.com/jasonweicn/MiniFramework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------
namespace Mini;

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
     * 函数库清单数组
     *
     * @var array
     */
    private static $_funcs = array();

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
     * @return obj
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
    protected function __construct()
    {
        $this->_params = Params::getInstance();
        $this->getRouter();
        
        $this->_request = Request::getInstance();
    }

    /**
     * 开始
     */
    public function run()
    {
        $requestParams = $this->_request->parseRequestParams($this->_router->getRouteType());
        
        if (! empty($requestParams)) {
            $this->_params->setParams($requestParams);
        }
        
        $this->loadFunc('Global');
        
        if (DB_AUTO_CONNECT === true) {
            $this->initDbPool();
        }
        
        $this->dispatch();
    }

    /**
     * 调派
     */
    public function dispatch()
    {
        $request = Request::getInstance();
        $this->controller = $request->_controller;
        $this->action = $request->_action;
        
        $controllerName = ucfirst($this->controller);
        $isApi = (REST_ON === true && $controllerName == 'Api') ? true : false;
        
        if ($isApi === true) {
            
            $apiName = ucfirst($this->action);
            $apiFile = APP_PATH . DS . 'Api' . DS . $apiName . '.php';
            
            if (! file_exists($apiFile)) {
                throw new Exceptions('Api file "' . $apiFile . '" not found.', 404);
            }
            
            $apiName = APP_NAMESPACE . '\\Api\\' . $apiName;
            
            if ('Mini\\Rest' !== get_parent_class($apiName)) {
                throw new Exceptions('Api "' . $apiName . '" not extends "Rest" class.');
            }
            
            if (class_exists($apiName)) {
                $api = new $apiName();
            } else {
                throw new Exceptions('Api "' . $apiName . '" does not exist.', 404);
            }
        } else {
            
            $controllerFile = APP_PATH . DS . 'Controller' . DS . $controllerName . '.php';
            
            if (! file_exists($controllerFile)) {
                throw new Exceptions('Controller file "' . $controllerFile . '" not found.', 404);
            }
            
            $controllerName = APP_NAMESPACE . '\\Controller\\' . $controllerName;
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
            } else {
                throw new Exceptions('Controller "' . $controllerName . '" does not exist.', 404);
            }
            
            $action = $this->action . 'Action';
            
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                throw new Exceptions('Action "' . $this->action . '" does not exist.', 404);
            }
        }
    }

    /**
     * 获取路由器对象
     *
     * @return obj
     */
    public function getRouter()
    {
        if ($this->_router === null) {
            $this->_router = new Router();
        }
        return $this->_router;
    }

    /**
     * 加载函数库
     *
     * @param string $func            
     * @throws Exception
     * @return boolean
     */
    private function loadFunc($func)
    {
        $file = MINI_PATH . DS . 'Functions' . DS . ucfirst($func) . '.func.php';
        
        $key = md5($file);
        if (! isset(self::$_funcs[$key])) {
            if (file_exists($file)) {
                include ($file);
                self::$_funcs[$key] = true;
            } else {
                throw new Exceptions('Function "' . $func . '" not found.');
            }
        }
        
        return true;
    }

    /**
     * 初始化数据库对象池
     * 
     * @throws Exceptions
     * @return boolean
     */
    private function initDbPool()
    {
        $dbConfig = Config::getInstance()->load('database');
        if (is_array($dbConfig)) {
            foreach ($dbConfig as $dbKey => $dbParams) {
                $this->_dbPool[$dbKey] = Db::factory('Mysql', $dbParams);
            }
        } else {
            throw new Exceptions('Config "database" invalid.');
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
