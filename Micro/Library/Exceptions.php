<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
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
            $this->_throwExceptions = $flag;
        }
        return $this->_throwExceptions;
    }
}