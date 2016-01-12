<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

abstract class Cache_Abstract
{
    /**
     * Exceptions实例
     * @var Exceptions
     */
    protected $_exception;
    
    /**
     * 数据库连接参数
     * 
     * @var array
     */
    protected $_params = array();
    
    /**
     * 缓存过期时间（单位：秒）
     * 
     * @var int
     */
    protected $_expire = 1800;
    
    /**
     * 是否压缩缓存数据
     * @var bool
     */
    protected $_compress = true;
    
    /**
     * 缓存服务器
     * 
     * @var object | resource | null
     */
    protected $_cache_server = null;
    
    /**
     * 读取缓存
     * 
     * @param string $name
     */
    abstract protected function get($name);
    
    /**
     * 写入缓存
     * 
     * @param string $name
     * @param mixed $value
     * @param int $expire
     */
    abstract protected function set($name, $value, $expire = null);
    
    /**
     * 清除缓存
     * 
     * @param string $name
     */
    abstract protected function del($name);
    
    /**
     * 构造
     * 
     * @param array $params => array (
     *     host     => (string) 主机，默认值为空
     *     port     => (int) 端口，默认值为空
     *     
     *     prefix   => (string) 缓存名前缀，默认值为空
     * )
     * @return Cache_Abstract
     */
    public function __construct($params)
    {
        $this->_exception = Exceptions::getInstance();
        
        if (!is_array($params)) {
            if ($this->_exception->throwExceptions()) {
                throw new Exception('Adapter params must be in an array.');
            } else {
                $this->_exception->sendHttpStatus(500);
            }
        }
        
        $this->_params = $params;
    }
}