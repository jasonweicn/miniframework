<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Cache
{
    /**
     * 工厂模式获取缓存实例
     * 
     * @param string $adapter
     * @param array $params
     */
    public static function factory($adapter = 'Memcache', $params = array())
    {
        if (!is_string($adapter) || empty($adapter)) {
            throw new Exceptions('Adapter name must be specified in a string.');
        }
        
        if (in_array($adapter, array('Memcache', 'Redis'))) {
            
            if (!function_exists($adapter)) {
                throw new Exceptions('Class ' . $adapter . ' not found');
            }
            
            if (!is_array($params)) {
                throw new Exceptions('Cache params invalid.');
            }
            
            if (!isset($params['host'])) {
                throw new Exceptions('Cache(' . $adapter . ') host is not defined.');
            } elseif (!isset($params['port'])) {
                throw new Exceptions('Cache(' . $adapter . ') port is not defined.');
            }
        }
        
        $adapterName = 'Cache_' . ucwords($adapter);
        
        if (!class_exists($adapterName, false)) {
            $adapterPath = MINI_PATH . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'Cache';
            $adapterFile = $adapterPath . DIRECTORY_SEPARATOR . $adapterName . '.php';
            if (!file_exists($adapterFile)) {
                throw new Exceptions('Adapter "' . $adapterName . '" not found.');
            }
            
            require_once($adapterFile);
        }
        
        $cacheAdapter = new $adapterName($params);
        
        if (! $cacheAdapter instanceof Cache_Abstract) {
            throw new Exceptions('Adapter class "' . $adapterName . '" does not extend Cache_Abstract.');
        }

        return $cacheAdapter;
    }
}
