<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

namespace Mini;

class App
{
    /**
     * 控制器
     * @var string
     */
    public $controller;
    
    /**
     * 动作
     * @var string
     */
    public $action;
    
    /**
     * 函数库清单数组
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
     * App实例
     * 
     * @var App
     */
    protected static $_instance;
    
    /**
     * 获取实例
     * 
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
     * 
     */
    protected function __construct()
    {
        $this->_params = Params::getInstance();
        $this->getRouter();
    }
    
    /**
     * 开始
     * 
     */
    public function run()
    {
        if ('cli' == $this->_router->getRouteType()) {
            $cliParams = $this->_router->getCliParamsArray();
            if (!empty($cliParams)) {
                $this->_params->setParams($cliParams);
            }
        } elseif ('rewrite' == $this->_router->getRouteType()) {
            $this->uriToParams($this->_router->getUriArray());
        }
        
        $this->loadFunc('global');
        
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
        $controllerFile = APP_PATH . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . $controllerName . '.php';
                
        if (!file_exists($controllerFile)) {
            throw new Exceptions('Controller "' . $controllerFile . '" not found.', 404);
        }
        
        $controllerName = APP_NAMESPACE . '\\Controller\\' . $controllerName;
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
        } else {
            throw new Exceptions($controllerName . ' does not exist.', 404);
        }
        
        $action = $this->action . 'Action';
        
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            throw new Exceptions('Action "' . $this->_router->_action . '" does not exist.', 404);
        }
    }
    
    /**
     * 获取路由器对象
     * 
     */
    public function getRouter()
    {
        if ($this->_router === null) {
            $this->_router = new Router();
        }
        return $this->_router;
    }
    
    /**
     * 提取地址中的参数
     * 
     * @param array $uriArray
     */
    private function uriToParams($uriArray = null)
    {
        $array = null;
        if (is_array($uriArray)) array_splice($uriArray, 0, 3);
        
        if (!empty($uriArray)) {
            foreach ($uriArray as $key => $value) {
                if ($key % 2 == 0) {
                    $array[$value] = null;
                } else {
                    $array[$uriArray[$key - 1]] = $value;
                }
            }
            
            foreach ($array as $key => $value) {
                if ($value !== null) {
                    $this->_params->setParam($key, $value);
                }
            }
        }
    }
    
    /**
     * 加载函数库
     * @param string $func
     * @throws Exception
     * @return boolean
     */
    private function loadFunc($func)
    {
        $file = MINI_PATH . DIRECTORY_SEPARATOR . 'Functions' . DIRECTORY_SEPARATOR . $func . '.func.php';
        $key = md5($file);
        if (!isset(self::$_funcs[$key])) {
            if (file_exists($file)) {
                include($file);
                self::$_funcs[$key] = true;
            } else {
                throw new Exceptions('Function "' . $func . '" not found.');
            }
        }
        
        return true;        
    }
}
