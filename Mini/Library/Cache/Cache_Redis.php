<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Cache_Redis extends Cache_Abstract
{
    /**
     * 连接Redis
     * 
     */
    protected function _connect()
    {
        if ($this->_cache_server) return;
        
        try {
            $this->_cache_server = new Redis();
            $this->_cache_server->connect($this->_params['host'], $this->_params['port']);
        } catch (Exceptions $e) {
            throw new Exceptions($e);
        }
        
        if (isset($this->_params['passwd'])) {
            $authStatus = $redis->auth($this->_params['passwd']);
            if ($authStatus === false) {
                throw new Exceptions('Redis connection failed.');
            }
        }
    }
    
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->_expire;
        }
        $this->_connect();
        $this->_cache_server->set($name, $value);
        if ($expire > 0) {
            $this->_cache_server->expire($name, $expire);
        }
    }
    
    public function get($name)
    {
        $this->_connect();
        return $this->_cache_server->get($name);
    }
    
    public function del($name)
    {
        $this->_connect();
        return $this->_cache_server->delete($name);
    }
    
    /**
     * 获取Redis实例化对象，便于使用其他未封装的方法
     * @return obj
     */
    public function getRedisObj()
    {
        $this->_connect();
        return $this->_cache_server;
    }
    
    /**
     * 关闭Redis连接
     */
    public function close()
    {
        try {
            $this->_cache_server->close();
            $this->_cache_server = null;
        } catch (Exceptions $e) {
            throw new Exceptions($e);
        }
    }
}
