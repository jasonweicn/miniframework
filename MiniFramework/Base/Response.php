<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2022 http://www.sunbloger.com
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

class Response
{
    /**
     * Response 实例
     * 
     * @var Response
     */
    protected static $_instance;

    /**
     * Header Object
     *
     * @var Object
     */
    private $_header;

    /**
     * Http Status Code
     * 
     * @var integer
     */
    private $_httpStatusCode = 200;

    /**
     * Content-Type
     * 
     * @var array
     */
    private $_contentType = [
        'html' => 'text/html',
        'text' => 'text/plain',
        'json' => 'application/json',
        'xml' => 'application/xml'
    ];

    /**
     * 输出方式
     * 
     * @var string
     */
    private $_type = 'html';

    /**
     * Http status
     *  
     * @var array
     */
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
     * 获取 Response 单例
     * 
     * @return \Mini\Base\Response
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }

    private function __clone()
    {}

    /**
     * Get response header object
     * 
     * @return object|\Mini\Base\Header
     */
    public function getHeader()
    {
        if ($this->_header === null) {
            $this->_header = new Header();
        }
        
        return $this->_header;
    }

    /**
     * Set response header data 
     * 
     * @param string $name
     * @param string $value
     * @return \Mini\Base\Response
     */
    public function header($name, $value)
    {
        $this->getHeader()->set($name, $value);
        
        return $this;
    }

    /**
     * Set http status
     *
     * @param int $code
     * @return boolean
     */
    public function httpStatus($code)
    {
        if (isset(self::$status[$code])) {
            $this->_httpStatusCode = $code;
        } else {
            throw new Exception('Invalid http status code: ' . $code);
        }
        
        return $this;
    }

    /**
     * 获取 HTTP STATUS 信息
     * 
     * @param int $code
     * @return string|boolean
     */
    public static function getStatusMsg($code)
    {
        if (isset(self::$status[$code])) {
            return self::$status[$code];
        }
        
        return false;
    }

    /**
     * Set Content-Type
     * 
     * @param string $type html|text|json|xml
     * @return \Mini\Base\Response
     */
    public function type($type)
    {
        if (isset($this->_contentType[$type])) {
            $this->_type = $type;
        } else {
            $this->_type = 'html';
        }
        
        return $this;
    }

    /**
     * 输出
     *
     * @param string $content
     */
    public function send($content = '')
    {
        if (SHOW_DEBUG === false) {
            ob_end_clean();
        }
        if (false === headers_sent()) {
            if (isset(self::$status[$this->_httpStatusCode])) {
                $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
                header($protocol . ' ' . $this->_httpStatusCode . ' ' . self::$status[$this->_httpStatusCode], true, $this->_httpStatusCode);
            } else {
                throw new Exception('Invalid http status code: ' . $this->_httpStatusCode);
            }
            $header = $this->getHeader();
            $headers = $header->getAll();
            foreach ($headers as $name => $value) {
                header($name . ': ' . $value);
            }
            if (! isset($headers['Content-Type'])) {
                header('Content-Type: ' . $this->_contentType[$this->_type]);
            }
            header('Cache-Control: ' . HTTP_CACHE_CONTROL);
        }
        
        echo $content;
        
        die();
    }
}
