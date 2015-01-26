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
    protected $_controller;
    protected $_router;
    protected $_baseUrl;
    
    protected static $_instance;
    
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    protected function __construct()
    {
        $router = $this->getRouter();
        $this->_controller = $router->route();
    }
    
    /**
     * 开始
     * 
     */
    public function run()
    {
        $action = $this->_router->_action . 'Action';
        
        if (method_exists($this->_controller, $action)) {
            if (method_exists($this->_controller, 'init')) {
                $this->_controller->init();
            }
            $this->_controller->$action();
        } else {
            throw new Exception('Action ' . $action . ' does not exist.');
        }
    }
    
    /**
     * 获取路由器对象
     * 
     */
    public function getRouter ()
    {
        if ($this->_router === null) {
            $this->_router = new Router();
        }
        return $this->_router;
    }
}