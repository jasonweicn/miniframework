<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2017 http://www.sunbloger.com
// +---------------------------------------------------------------------------
// | Licensed under the Apache License, Version 2.0 (the "License");
// | you may not use this file except in compliance with the License.
// | You may obtain a copy of the License at
// |
// |   http://www.apache.org/licenses/LICENSE-2.0
// |
// | Unless required by applicable law or agreed to in writing, software
// | distributed under the License is distributed on an "AS IS" BASIS,
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// | See the License for the specific language governing permissions and
// | limitations under the License.
// +---------------------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------

namespace Mini;

class Request
{
    /**
     * Request实例
     * 
     * @var Request
     */
    protected static $_instance;
    
    /**
     * 基础地址
     * 
     * @var string
     */
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
     * 
     * @var array
     */
    protected $_queryStringArray;
    
    /**
     * 请求参数数组
     * 
     * @var array
     */
    protected $_requestParams = array();
    
    /**
     * 获取实例
     *
     * @return obj
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
     * @return string
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
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->_baseUrl === null) {
            $this->_baseUrl = $this->setBaseUrl();
        }
        return $this->_baseUrl;
    }
    
    /**
     * 获取请求方法
     */
    public function method()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = $_SERVER['REQUEST_METHOD'];
        } elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } elseif (isset($_REQUEST['_method'])) {
            $method = $_REQUEST['_method'];
        } else {
            $method = 'GET';
        }
        
        return strtoupper($method);
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
     * 
     * @return array
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
     * 
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
