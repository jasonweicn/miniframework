<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

require_once 'Cache_Abstract.php';

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
        } catch (PDOException $e) {
            if ($this->_exception->throwExceptions()) {
                throw new Exception($e);
            } else {
                $this->_exception->sendHttpStatus(500);
            }
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
     * 关闭Memcache连接
     */
    public function close()
    {
        try {
            $this->_cache_server->close();
            $this->_cache_server = null;
        } catch (PDOException $e) {
            if ($this->_exception->throwExceptions()) {
                throw new Exception($e);
            } else {
                $this->_exception->sendHttpStatus(500);
            }
        }
    }
}
