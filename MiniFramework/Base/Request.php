<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2021 http://www.sunbloger.com
// +---------------------------------------------------------------------------
// | Licensed under the Apache License, Version 2.0 (the "License");
// | you may not use this file except in compliance with the License.
// | You may obtain a copy of the License at
// |
// | http://www.apache.org/licenses/LICENSE-2.0
// |
// | Unless required by applicable law or agreed to in writing, software
// | distributed under the License is distributed on an "AS IS" BASIS,
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// | See the License for the specific language governing permissions and
// | limitations under the License.
// +---------------------------------------------------------------------------
// | Source: https://github.com/jasonweicn/miniframework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------
namespace Mini\Base;

class Request
{

    /**
     * Request实例
     *
     * @var Request
     */
    protected static $_instance;
    
    /**
     * Http实例
     * 
     * @var Http
     */
    protected $_http;

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
    protected $_requestParams = [];

    /**
     * 存放请求的Header信息数组
     *
     * @var array
     */
    private $_headers = [];

    /**
     * 预处理过的REQUEST_URI
     *
     * @var string
     */
    private $_requestUri;
    
    /**
     * CSRF令牌存储参数名
     * 
     * @var string
     */
    private $_csrfParamName = '_mini-csrf-token';
    
    private $_csrfHeaderParamName = 'X-Mini-Csrf-Token';

    /**
     * 获取实例
     *
     * @return object
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
     */
    protected function __construct()
    {
        $this->_http = Http::getInstance();
    }

    private function __clone()
    {}

    /**
     * 从$_SERVER['PHP_SELF']中提取基础地址
     *
     * @return string
     */
    public function setBaseUrl()
    {
        if ($this->_baseUrl === null) {
            $urlArray = explode('/', $_SERVER['PHP_SELF']);
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
        
        $queryStringArray = [];
        parse_str($_SERVER['QUERY_STRING'], $queryStringArray);
        $this->_queryStringArray = $queryStringArray;
        
        return $queryStringArray;
    }

    /**
     * 解析请求参数
     *
     * @throws Exception
     * @return array
     */
    public function parseRequestParams($routeType)
    {
        $requestParams = [];
        
        if ($routeType == 'cli') {
            
            if (isset($_SERVER['argc']) && $_SERVER['argc'] > 2) {
                for ($i = 2; $i < $_SERVER['argc']; $i ++) {
                    if (strpos($_SERVER['argv'][$i], '=') > 0) {
                        $curParam = explode('=', $_SERVER['argv'][$i]);
                        $requestParams[$curParam[0]] = $curParam[1];
                    } else {
                        throw new Exception('Request params invalid.');
                    }
                }
            }
        } elseif ($routeType == 'rewrite') {
            
            $requestUri = $this->getRequestUri();
            
            if (! empty($_SERVER['QUERY_STRING'])) {
                $queryStringArray = $this->getQueryStringArray();
            }
            
            if ($requestUri != $this->_baseUrl) {
                $requestUri = str_replace($this->_baseUrl, '', $requestUri);
            }
            
            $uriArray = explode('/', $requestUri);
            
            $array = null;
            array_splice($uriArray, 0, 3);
            
            if (! empty($uriArray)) {
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
            
            if (! empty($queryStringArray)) {
                $requestParams = array_merge($requestParams, $queryStringArray);
            }
        } elseif ($routeType == 'get') {
            
            if (! empty($_SERVER['QUERY_STRING'])) {
                $requestParams = $this->getQueryStringArray();
            }
        }
        
        return $requestParams;
    }

    /**
     * 获取请求Header信息数组
     *
     * @param string $name            
     * @return multitype:
     */
    public function getHeaders($name = null)
    {
        if (empty($this->_headers)) {
            foreach ($_SERVER as $key => $val) {
                if ('HTTP_' == substr($key, 0, 5)) {
                    $this->_headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $val;
                }
            }
        }
        
        if (isset($this->_headers[$name])) {
            return $this->_headers[$name];
        }
        
        return $this->_headers;
    }

    /**
     * 获取预处理后的REQUEST_URI
     *
     * @return string
     */
    public function getRequestUri()
    {
        if ($this->_requestUri === null) {
            $requestUri = '';
            if (empty($_SERVER['QUERY_STRING'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
            } else {
                $requestUri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
            }
            
            // 遇到常量定义的伪静态扩展名结尾时，转换可能存在的伪静态分隔符
            $match = [];
            if (preg_match('/\.(' . URL_SUFFIX . ')$/', $requestUri, $match)) {
                $requestUri = substr($requestUri, 0, - (strlen($match[1])+1));
                $pos = strrpos($requestUri, '/');
                $dir = substr($requestUri, 0, $pos);
                $file = substr($requestUri, $pos);
                $file = str_replace(URL_SPLIT_SYMBOL, '/', $file);
                $requestUri = $dir . $file;
            }
            
            $this->_requestUri = $requestUri;
        }
        
        return $this->_requestUri;
    }
    
    /**
     * 获取CSRF令牌存储键名
     * 
     * @return string
     */
    public function getCsrfParamName()
    {
        return $this->_csrfParamName;
    }
    
    /**
     * 创建CSRF令牌
     * 
     * @param string $type
     * @return string
     */
    public function createCsrfToken($type = 'cookie')
    {
        $token = getRandomString(64);
        
        Session::start();
        Session::set($this->_csrfParamName, $token);
        Session::commit();
        
        if ($type == 'cookie') {
            setcookie($this->_csrfParamName, $token, 0, '/', null, null, true);
        } elseif ($type == 'header') {
            $this->_http->header($this->_csrfHeaderParamName, $token);
        }
        
        
        return $token;
    }
    
    /**
     * 读取CSRF令牌
     * 
     * @param string $type (cookie | session | header | get | post)
     * @return string
     */
    public function loadCsrfToken($type = 'cookie')
    {
        $token = '';
        
        if ($type == 'cookie') {
            if (isset($_COOKIE[$this->_csrfParamName])) {
                $token = $_COOKIE[$this->_csrfParamName];
            }
        } elseif ($type == 'session') {
            Session::start();
            $token = Session::get($this->_csrfParamName);
        } elseif ($type == 'header') {
            $token = $this->getHeaders($this->_csrfHeaderParamName);
            if (! is_string($token)) {
                $token = '';
            }
        } elseif ($type == 'get') {
            if (isset($_GET[$this->_csrfParamName])) {
                $token = $_GET[$this->_csrfParamName];
            }
        } elseif ($type == 'post') {
            if (isset($_POST[$this->_csrfParamName])) {
                $token = $_POST[$this->_csrfParamName];
            }
        }
        
        return $token;
    }
    
    /**
     * 校验客户端传入的CSRF令牌
     * 
     * @throws Exception
     * @return boolean
     */
    public function checkCsrfToken()
    {
        $clientCsrfToken = $this->loadCsrfToken('cookie');
        
        if (! $clientCsrfToken) {
            $clientCsrfToken = $this->loadCsrfToken('header');
        }
        
        if (! $clientCsrfToken) {
            $clientCsrfToken = $this->loadCsrfToken('get');
        }
        
        if (! $clientCsrfToken) {
            $clientCsrfToken = $this->loadCsrfToken('post');
        }
        
        if (! $clientCsrfToken) {
            throw new Exception('Client CSRF-Token invalid.');
        }
        
        $serverCsrfToken = $this->loadCsrfToken('session');
        
        if (! $serverCsrfToken) {
            throw new Exception('Server CSRF-Token does not exist.');
        }
        
        if ($clientCsrfToken === $serverCsrfToken) {
            return true;
        }
        
        return false;
    }
}
