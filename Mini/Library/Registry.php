<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Registry extends ArrayObject
{
    /**
     * Registry Instance
     * 
     * @var Registry
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
     * 存入
     * 
     * @param string $index
     * @param mixed $value
     */
    public static function set($index, $value)
    {
        $instance = self::getInstance();
        $instance->offsetSet($index, $value);
    }
    
    /**
     * 读出
     * 
     * @param string $index
     * @return mixed
     */
    public static function get($index)
    {
        $instance = self::getInstance();
        
        if (!$instance->offsetExists($index)) {
            $exceptions = Exceptions::getInstance();
            if ($exceptions->throwExceptions()) {
                throw new Exception('"' . $index . '" not registered.');
            } else {
                $this->_exception->sendHttpStatus(500);
            }
        }
        
        return $instance->offsetGet($index);
    }
}
