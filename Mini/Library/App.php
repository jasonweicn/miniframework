<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

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
     * Exceptions实例
     * @var Exceptions
     */
    protected $_exception;
    
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
        $this->_exception = Exceptions::getInstance();
        $this->_exception->throwExceptions(SHOW_ERROR);
        $this->_params = Params::getInstance();
        $this->getRouter();
    }
    
    /**
     * 开始
     * 
     */
    public function run()
    {
        if ('rewrite' == $this->_router->getRouteType()) {
            $this->uriToParams($this->_router->getUriArray());
        }
        
        $this->loadFunc('global');
        
        $this->dispatch();
    }
    
    /**
     * 调派
     */
    private function dispatch()
    {
        $this->controller = $this->_router->_controller;
        $this->action = $this->_router->_action;
        
        $controllerName = ucfirst($this->controller) . 'Controller';
        $controllerFile = APP_PATH . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php';
        
        if (file_exists($controllerFile)) {
            include_once($controllerFile);
        } else {
            if ($this->_exception->throwExceptions()) {
                throw new Exception('Controller "' . $controllerFile . '" not found.');
            } else {
                $this->_exception->sendHttpStatus(404);
            }
        }
        
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
        } else {
            if ($this->_exception->throwExceptions()) {
                throw new Exception($controllerName . ' does not exist.');
            } else {
                $this->_exception->sendHttpStatus(404);
            }
        }
        
        $action = $this->_router->_action . 'Action';
        
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            if ($this->_exception->throwExceptions()) {
                throw new Exception('Action "' . $this->_router->_action . '" does not exist.');
            } else {
                $this->_exception->sendHttpStatus(404);
            }
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
        if (!isset(self::$funcs[$key])) {
            if (file_exists($file)) {
                include($file);
                self::$funcs[$key] = true;
            } else {
                throw new Exception('Function "' . $func . '" not found.');
            }
        }
        
        return true;        
    }
}