<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class View
{
    /**
     * Micro_Exception实例
     * @var Micro_Exception
     */
    protected $_exception;
    
    /**
     * 控制器
     * @var string
     */
    protected $_controller;
    
    /**
     * 动作
     * @var string
     */
    protected $_action;
    
    /**
     * 渲染模板
     * 
     * @var string
     */
    private $_render;
    
    /**
     * App实例
     * @var App
     */
    protected $_app;
    
    /**
     * 基础路径
     * @var string
     */
    protected $_baseUrl;
    
    /**
     * 构造
     * 
     * @param string $controller
     * @param string $action
     * @return View
     */
    function __construct($controller, $action)
    {
        $this->_exception = Micro_Exception::getInstance();
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_app = App::getInstance();
    }
    
    public function baseUrl()
    {
        if ($this->_baseUrl === null) {
            $this->_baseUrl = $this->_app->_baseUrl;
        }
        return $this->_baseUrl;
    }
    
    public function __set($variable, $value)
    {
        $this->assign($variable, $value);
    }

    /**
     * 接收来自于控制器的变量
     * 
     * @param string $variable
     * @param mixed $value
     */
    public function assign($variable, $value)
    {
        if (substr($variable, 0, 1) != '_') {
            $this->$variable = $value;
            return true;
        }
        return false;
    }
    
    /**
     * 渲染
     * 
     */
    public function display()
    {
        $file = APP_PATH . DIRECTORY_SEPARATOR .  'Views' . DIRECTORY_SEPARATOR . strtolower($this->_controller) . DIRECTORY_SEPARATOR . $this->_action . '.php';
        
        if (file_exists($file)) {
            $this->_render = $file;
        } else {
            if ($this->_exception->throwExceptions()) {
                throw new Exception('View "' . $this->_action . '" does not exist.');
            }
        }
        
        include($this->_render);
    }
}
