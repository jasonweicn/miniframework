<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

require_once 'Cache_Abstract.php';

class Cache_File extends Cache_Abstract
{
    /**
     * 写入缓存
     * {@inheritDoc}
     * @see Cache_Abstract::set()
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->_expire;
        }
        $cache_key = $this->getCacheKey($name);
        $cache_value = serialize($value);
        $compress_flag = 0;
        if ($this->_compress && function_exists('gzcompress')) {
            $cache_value = gzcompress($cache_value);
            $compress_flag = 1;
        }
        $cache_value = sprintf('%012d', $expire) . $compress_flag . $cache_value;
        
        if (!file_exists(CACHE_PATH) && !is_dir(CACHE_PATH)) {
            mkdir(CACHE_PATH, 0744, true);
        }
        file_put_contents(CACHE_PATH . DIRECTORY_SEPARATOR . $cache_key, $cache_value);
    }
    
    /**
     * 读取缓存
     * {@inheritDoc}
     * @see Cache_Abstract::get()
     */
    public function get($name)
    {
        $cache_key = $this->getCacheKey($name);
        $cache_file = CACHE_PATH . DIRECTORY_SEPARATOR . $cache_key;
        if (!file_exists($cache_file)) {
            return false;
        }
        $cache_value = file_get_contents($cache_file);
        
        if ($cache_value === false) {
            return false;
        }
        
        $expire = (int)substr($cache_value, 0, 12);
        if ($expire != 0 && time() > filemtime($cache_file) + $expire) {
            unlink($cache_file);
            return false;
        }
        
        $compress_flag = (int)substr($cache_value, 12, 1);
        if ($compress_flag == 1 && function_exists('gzcompress')) {
            $value = unserialize(gzuncompress(substr($cache_value, 13)));
        } else {
            $value = unserialize(substr($cache_value, 13));
        }
        
        return $value;        
    }
    
    /**
     * 删除缓存
     * {@inheritDoc}
     * @see Cache_Abstract::del()
     */
    public function del($name)
    {
        $cache_key = $this->getCacheKey($name);
        $cache_file = CACHE_PATH . DIRECTORY_SEPARATOR . $cache_key;
        @unlink($cache_file);
    }
    
    public function close()
    {
        
    }
    
    /**
     * 获取缓存文件KEY值
     * @param unknown $name
     * @return string
     */
    private function getCacheKey ($name)
    {
        return md5($name);
    }
}
