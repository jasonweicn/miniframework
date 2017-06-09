<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

namespace Mini\Cache;

use \Memcache;

class Cache_Memcache extends Cache_Abstract
{
    /**
     * 连接Memcache
     * 
     */
    protected function _connect()
    {
        if ($this->_cache_server) return;
        
        try {
            $this->_cache_server = new Memcache();
            $this->_cache_server->connect($this->_params['host'], $this->_params['port']);
        } catch (Exceptions $e) {
            throw new Exceptions($e);
        }
        
        $memStats = $this->_cache_server->getExtendedStats();
        $available = (bool) $memStats[$this->_params['host'] . ':' . $this->_params['port']];
        if (!$available) {
            throw new Exceptions('Memcached connection failed.');
        }
    }
    
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->_expire;
        }
        $compress_flag = $this->_compress_flag ? MEMCACHE_COMPRESSED : 0;
        $this->_connect();
        $this->_cache_server->set($name, $value, $compress_flag, $expire);
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
     * 获取Memcache实例化对象，便于使用其他未封装的方法
     * @return obj
     */
    public function getMemcacheObj()
    {
        $this->_connect();
        return $this->_cache_server;
    }
    
    /**
     * 关闭Memcache连接
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
