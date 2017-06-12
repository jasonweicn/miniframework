<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

namespace Mini;

class Request
{
    /**
     * Request实例
     * 
     * @var Request
     */
    protected static $_instance;
    
    protected $_baseUrl = null;
    
    /**
     * 控制器
     * 
     * @var string
     */
    public $_controller;
    
    /**
     * 动作
     * 
     * @var string
     */
    public $_action;
    
    /**
     * QUERY_STRING转化的数组
     * @var array
     */
    protected $_queryStringArray;
    
    /**
     * 请求参数数组
     * @var array
     */
    protected $_requestParams = array();
    
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
        //reserve...
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
    
    /**
     * 设置控制器
     * 
     * @param string $value
     */
    public function setControllerName($value)
    {
        $this->_controller = $value;
    }
    
    /**
     * 设置动作
     * 
     * @param string $value
     */
    public function setActionName($value)
    {
        $this->_action = $value;
    }
    
    /**
     * 获取QUERY_STRING数组
     */
    public function getQueryStringArray()
    {
        if (isset($this->_queryStringArray)) {
            return $this->_queryStringArray;
        }
    
        $queryStringArray = array();
        parse_str($_SERVER['QUERY_STRING'], $queryStringArray);
        $this->_queryStringArray = $queryStringArray;
    
        return $queryStringArray;
    }
    
    /**
     * 解析请求参数
     * @throws Exceptions
     * @return array
     */
    public function parseRequestParams($routeType)
    {
        $requestParams = array();
        
        if ($routeType == 'cli') {
            
            if ($_SERVER['argc'] > 2) {
                for ($i=2; $i<$_SERVER['argc']; $i++) {
                    if (strpos($_SERVER['argv'][$i], '=') > 0) {
                        $curParam = explode('=', $_SERVER['argv'][$i]);
                        $requestParams[$curParam[0]] = $curParam[1];
                    } else {
                        throw new Exceptions('Request params invalid.');
                    }
                }
            }
        
        } elseif ($routeType == 'rewrite') {
            
            $requestUri = '';
            
            if (empty($_SERVER['QUERY_STRING'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
            } else {
                $requestUri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
                $queryStringArray = $this->getQueryStringArray();
            }
            
            if ($requestUri != $this->_baseUrl) {
                $requestUri = str_replace($this->_baseUrl, '', $requestUri);
            }
            
            $uriArray = explode('/', $requestUri);
            
            $array = null;
            array_splice($uriArray, 0, 3);
            
            if (!empty($uriArray)) {
                foreach ($uriArray as $key => $value) {
                    if ($key % 2 == 0) {
                        $array[$value] = null;
                    } else {
                        $array[$uriArray[$key - 1]] = $value;
                    }
                }
                foreach ($array as $key => $value) {
                    if ($key != '' && $value !== null) {
                        $requestParams[$key] = $value;
                    }
                }
            }
            
            if (!empty($queryStringArray)) {
                $requestParams = array_merge($requestParams, $queryStringArray);
            }
        
        } elseif ($routeType == 'get') {
            
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestParams = $this->getQueryStringArray();
            }
        
        }
        
        return $requestParams;
    }
}
