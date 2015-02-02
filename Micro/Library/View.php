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
     * Exceptions实例
     * @var Exceptions
     */
    private $_exception;
    
    /**
     * 控制器
     * @var string
     */
    private $_controller;
    
    /**
     * 动作
     * @var string
     */
    private $_action;
    
    /**
     * 渲染模板
     * 
     * @var string
     */
    private $_render;
    
    /**
     * Request实例
     * @var Request
     */
    private $_request;
    
    /**
     * 基础路径
     * @var string
     */
    protected $_baseUrl;
    
    /**
     * 构造
     */
    function __construct()
    {
        $this->_exception = Exceptions::getInstance();
        $this->_request = Request::getInstance();
        $app = App::getInstance();
        $this->_controller = $app->controller;
        $this->_action = $app->action;
    }
    
    public function baseUrl()
    {
        if ($this->_baseUrl === null) {
            $this->_baseUrl = $this->_request->getBaseUrl();
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
        $viewTpl = APP_PATH . DIRECTORY_SEPARATOR .  'Views' . DIRECTORY_SEPARATOR . strtolower($this->_controller) . DIRECTORY_SEPARATOR . $this->_action . '.php';
        
        if (file_exists($viewTpl)) {
            $this->_render = $viewTpl;
        } else {
            if ($this->_exception->throwExceptions()) {
                throw new Exception('View "' . $this->_action . '" does not exist.');
            }
        }
        
        include($this->_render);
    }
}
