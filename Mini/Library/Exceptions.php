<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Exceptions
{
    /**
     * 是否抛出异常
     * @var boolean
     */
    protected $_throwExceptions = false;
    
    protected static $status = array(
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
            302 => 'Found',  // 1.1
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
    );
    
    /**
     * Exceptions Instance
     *
     * @var Exceptions
     */
    protected static $_instance;
    
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
     * 异常处理方法
     * @param boolean $flag
     * @return boolean
     */
    public function throwExceptions($flag = null)
    {
        if ($flag !== null) {
            $this->_throwExceptions = ($flag === true) ? $flag : false;
        }
        return $this->_throwExceptions;
    }
    
    /**
     * 发送http状态
     */
    public function sendHttpStatus($code)
    {
        if (isset(self::$status[$code])) {
            $info = '<html><head><title>Error</title></head><body><h1>An error occurred</h1>';
            $info.= '<h2>' . $code . ' ' . self::$status[$code] . '</h2></body></html>';
            echo $info;
            header('HTTP/1.1 ' . $code . ' ' . self::$status[$code]);
            header('Status: ' . $code . ' ' . self::$status[$code]);
        }
        
        die();
    }
}
