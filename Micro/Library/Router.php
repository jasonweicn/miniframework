<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Router
{
    /**
     * Exceptions实例
     * @var Exceptions
     */
    protected $_exception;
    
    /**
     * Request实例
     * @var Request
     */
    private $_request;
    
    /**
     * 控制器实例
     * 
     * @var object
     */
    protected $_controller;
    
    /**
     * 动作
     * 
     * @var string
     */
    public $_action;
    
    /**
     * 基础路径
     * 
     * @var string
     */
    protected $_baseUrl;
    
    /**
     * 路由方式
     * 
     * @var string
     */
    public $_routeType;
    
    /**
     * 路径数组
     * 
     * @var string
     */
    public $_uriArray;
    
    /**
     * 构造
     * 
     */
    public function __construct()
    {
        $this->_exception = Exceptions::getInstance();
        $this->_request = Request::getInstance();
    }
    
    /**
     * 处理请求
     */
    private function _parser()
    {
        if (false === strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            //Rewrite
            $this->setRouteType('rewrite');
            $this->_uriArray = $this->parseUrlToArray();
            $this->_controller = (isset($this->_uriArray[1]) && !empty($this->_uriArray[1])) ? $this->_uriArray[1] : 'index';
            $this->_action = (isset($this->_uriArray[2]) && !empty($this->_uriArray[2])) ? $this->_uriArray[2] : 'index';
        } else {
            //GET
            $this->setRouteType('get');
            if (empty($_SERVER['QUERY_STRING'])) {
                $this->_controller = 'index';
                $this->_action = 'index';
            } else {
                parse_str($_SERVER['QUERY_STRING'], $urlParams);
                $this->_controller = isset($urlParams['c']) ? $urlParams['c'] : 'index';
                $this->_action = isset($urlParams['a']) ? $urlParams['a'] : 'index';
            }
        }
    }
    
    /**
     * 路由
     * 
     */
    public function route()
    {
        $this->_parser();
        
        $class = $this->_controller;
        $target = APP_PATH . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $class . '.php';
        
        if (file_exists($target)) {
            include_once($target);
            
            $className = ucfirst($class) . 'Controller';
            if (class_exists($className)) {
                $controller = new $className($class, $this->_action);
            } else {
                if ($this->_exception->throwExceptions()) {
                    throw new Exception($className . ' does not exist.');
                }
            }
        } else {
            if ($this->_exception->throwExceptions()) {
                throw new Exception('Controller "' . $class . '" not found.');
            }
        }
        return $controller;
    }
    
    public function setRouteType($type)
    {
        $this->_routeType = $type;
    }
    
    public function getRouteType()
    {
        return $this->_routeType;
    }
    
    /**
     * 解析Url为数组
     * 
     */
    public function parseUrlToArray()
    {
        $requestUri = '';
        
        if (empty($_SERVER['QUERY_STRING'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } else {
            $requestUri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
        }
        
        $baseUrl = $this->_request->getBaseUrl();
        
        if ($requestUri != $baseUrl) {
            $requestUri = str_replace($baseUrl, '', $requestUri);
        }
        $uriArray = explode('/', $requestUri);
        return $uriArray;
    }
}