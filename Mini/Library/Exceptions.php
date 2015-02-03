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
    
    /**
     * App实例
     *
     * @var App
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
        $status = array(
            404 => 'Not Found',
            500 => 'Internal Server Error'
        );
        
        if (isset($status[$code])) {
            $info = '<html><head><title>Error</title></head><body><h1>An error occurred</h1>';
            $info.= '<h2>' . $code . ' ' . $status[$code] . '</h2></body></html>';
            echo $info;
            header('HTTP/1.1 ' . $code . ' ' . $status[$code]);
            header('Status: ' . $code . ' ' . $status[$code]);
        }
        
        die();
    }
}