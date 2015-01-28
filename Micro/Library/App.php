<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class App
{
    /**
     * 控制器实例
     * 
     * @var object
     */
    protected $_controller;
    
    /**
     * Micro_Exception实例
     * @var Micro_Exception
     */
    protected $_exception;
    
    /**
     * Router实例
     * 
     * @var Router
     */
    protected $_router;
    
    /**
     * 基础路径
     * 
     * @var string
     */
    public $_baseUrl;
    
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
        $this->_exception = Micro_Exception::getInstance();
        $this->_params = Params::getInstance();
        $this->getRouter();
    }
    
    /**
     * 开始
     * 
     */
    public function run()
    {
        if (SHOW_ERROR === true) $this->_exception->throwExceptions(true);
        if ($this->_router->_routeType == 'rewrite') {
            $this->uriToParams($this->_router->_uriArray);
        }
        $this->_baseUrl = $this->_router->getBaseUrl();
        $this->_controller = $this->_router->route();
        $action = $this->_router->_action . 'Action';
        
        if (method_exists($this->_controller, $action)) {
            if (method_exists($this->_controller, 'init')) {
                $this->_controller->init();
            }
            $this->_controller->$action();
        } else {
            if ($this->_exception->throwExceptions()) {
                throw new Exception('Action "' . $this->_router->_action . '" does not exist.');
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
}