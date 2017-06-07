<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Exceptions extends \Exception
{
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
     * 构造
     *
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
    
    /**
     * 重构 toString
     */
    public function __toString()
    {
        if (SHOW_ERROR === true) {
            return parent::__toString();
        } else {
            if (isset(self::$status[$this->code])) {
                self::showErrorPage($this->code);
            } else {
                self::showErrorPage(500);
            }
        }
    }
    
    /**
     * 显示自定义的报错内容
     * 
     * @param int $code
     */
    private function showErrorPage($code)
    {
        $flag = self::sendHttpStatus($code);
        if ($flag === true) {
            $errMsg = self::$status[$code];
        } else {
            $errMsg = 'unknown error';
        }
        $info = '<html><head><title>Error</title></head><body><h1>An error occurred</h1>';
        $info.= '<h2>' . $code . ' ' . $errMsg . '</h2></body></html>';
        echo $info;
        
        die();
    }
    
    /**
     * 发送http状态
     */
    public function sendHttpStatus($code)
    {
        $flag = false;
        if (isset(self::$status[$code])) {
            header('HTTP/1.1 ' . $code . ' ' . self::$status[$code]);
            header('Status: ' . $code . ' ' . self::$status[$code]);
            $flag = true;
        }
        
        return $flag;
    }
}
