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
namespace Mini\Console;

use Mini\Base\Exception;
use Mini\Base\Router;
use Mini\Base\Config;
use Mini\Base\Params;
use Mini\Base\Request;

class App
{

    /**
     * Base App Class
     */
    private $baseApp;

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
     * 是否为 API 接口请求
     * 
     * @var boolean
     */
    public $isApi;

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
        $this->baseApp = \Mini\Base\App::getInstance();
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
        if ($this->_router === null) {
            $this->_router = new Router();
        }
        $target = $this->_router->route(Config::getInstance()->load('route', false));
        $this->baseApp->setController($target['c']);
        $this->baseApp->setAction($target['a']);
        $requestParams = $this->_request->parseRequestParams($this->_router->getRouteType());
        $isCli = $this->_router->isCli();
        if ($isCli === false) {
            throw new Exception(
                'The current script needs to run in console mode.'
            );
        }
        unset($this->_router);
        if (! empty($requestParams)) {
            $this->_params->setParams($requestParams);
        }
        
        // include global function file.
        include (MINI_PATH . DS . 'Function' . DS . 'Global.func.php');
        
        if (DB_AUTO_CONNECT === true) {
            $this->baseApp->initDbPool();
        }
        
        return $this->dispatch();
    }

    /**
     * 调派
     */
    public function dispatch($arguments = null)
    {
        $controllerName = ucfirst($this->baseApp->controller);

        $controllerFile = APP_PATH . DS . 'Console' . DS . $controllerName . '.php';
            
        if (! file_exists($controllerFile)) {
            throw new Exception(
                'Controller file "' . $controllerFile . '" not found.'
            );
        }
        
        $controllerName = APP_NAMESPACE . '\\Console\\' . $controllerName;
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
        } else {
            throw new Exception(
                'Controller "' . $controllerName . '" does not exist.'
            );
        }
        
        $action = $this->baseApp->action . 'Action';
        
        if (method_exists($controller, $action)) {
            return $controller->$action($arguments);
        } else {
            throw new Exception(
                'Action "' . $this->baseApp->action . '" does not exist.'
            );
        }
    }
}
