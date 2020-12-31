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

class Http
{

    protected static $status = [
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    ];

    /**
     * Http实例
     *
     * @var Http
     */
    protected static $_instance;

    /**
     * 用于输出的Header信息数组
     *
     * @var array
     */
    protected $_headers = [];

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
     * 写入头信息
     * 
     * @param string $name            
     * @param string $value            
     * @return \Mini\Base\Http
     */
    public function header($name, $value = null)
    {
        $this->_headers[$name] = $value;
        
        return $this;
    }
    
    /**
     * 输出
     * 
     * @param int $code
     * @param string $content
     * @throws \Exception
     */
    public function response($code, $content)
    {
        if (! isset(self::$status[$code])) {
            throw new \Exception('Invalid http status code: ' . $code);
        }
        
        if (SHOW_DEBUG === false) {
            ob_end_clean();
        }
        
        if (false === headers_sent()) {
            
            $this->sendHttpStatus($code);
            
            foreach ($this->_headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }
        
        if (! isset($this->_headers['Content-Type'])) {
            header("Content-Type: text/html; charset=utf-8");
        }
        header('Cache-Control: ' . HTTP_CACHE_CONTROL);
        
        echo $content;
        
        die();
    }

    /**
     * 发送HTTP状态
     * 
     * @param int $code
     * @return boolean
     */
    public static function sendHttpStatus($code)
    {
        $flag = false;
        if (isset(self::$status[$code])) {
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header($protocol . ' ' . $code . ' ' . self::$status[$code], true, $code);
            $flag = true;
        }
        
        return $flag;
    }

    /**
     * 判断HTTP状态是否存在
     *
     * @param int $code            
     * @return multitype:string |boolean
     */
    public static function isStatus($code)
    {
        if (isset(self::$status[$code])) {
            return self::$status[$code];
        }
        
        return false;
    }
}
