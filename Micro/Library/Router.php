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
    public $_baseUrl;
    
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
        $baseUrl = $this->getBaseUrl();
        
        if (false === strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            //Rewrite
            $this->setRouteType('rewrite');
            $this->_uriArray = $this->parserUrlToArray();
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
        $class = $this->_controller;
        $target = APP_PATH . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $class . '.php';
        
        //引入目标文件
        if (file_exists($target)) {
            include_once($target);
            
            //Example: NewsController
            $className = ucfirst($class) . 'Controller';
            if (class_exists($className)) {
                $controller = new $className($class, $this->_action);
            } else {
                throw new Exception($className . ' does not exist.');
            }
        } else {
            throw new Exception('Controller ' . $class . ' not found.');
        }
        return $controller;
    }
    
    /**
     * 从$_SERVER['PHP_SELF']中提取基础地址
     * 
     */
    public function setBaseUrl()
    {
        if ($this->_baseUrl === null) {
            $phpSelf = $_SERVER['PHP_SELF'];
            $urlArray = explode('/', $phpSelf);
            unset($urlArray[count($urlArray) - 1]);
            $this->_baseUrl = implode('/', $urlArray);
        }
        return $this->_baseUrl;
    }
    
    /**
     * 获取基础地址
     * 
     */
    public function getBaseUrl()
    {
        if ($this->_baseUrl === null) {
            $this->_baseUrl = $this->setBaseUrl();
        }
        return $this->_baseUrl;
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
    public function parserUrlToArray()
    {
        $baseUrl = $this->getBaseUrl();
        $requestUri = '';
        if (empty($_SERVER['QUERY_STRING'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } else {
            $requestUri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
        }
        if ($requestUri != $baseUrl) {
            $requestUri = str_replace($baseUrl, '', $requestUri);
        }
        $uriArray = explode('/', $requestUri);
        return $uriArray;
    }
}