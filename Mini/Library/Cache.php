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
        $exceptions = Exceptions::getInstance();
        
        if (!is_array($params)) {
            if ($exceptions->throwExceptions()) {
                throw new Exception('Adapter params must be in an array.');
            } else {
                $exceptions->sendHttpStatus(500);
            }
        }
        
        if (!is_string($adapter) || empty($adapter)) {
            if ($exceptions->throwExceptions()) {
                throw new Exception('Adapter name must be specified in a string.');
            } else {
                $exceptions->sendHttpStatus(500);
            }
        }
        
        $adapterName = 'Cache_' . ucwords($adapter);
        
        if (!class_exists($adapterName, false)) {
            $adapterPath = MINI_PATH . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'Cache';
            $adapterFile = $adapterPath . DIRECTORY_SEPARATOR . $adapterName . '.php';
            if (!file_exists($adapterFile)) {
                if ($exceptions->throwExceptions()) {
                    throw new Exception('Adapter "' . $adapterName . '" not found.');
                } else {
                    $exceptions->sendHttpStatus(500);
                }
                
            }
            
            require_once($adapterFile);
        }
        
        $cacheAdapter = new $adapterName($params);
        
        if (! $cacheAdapter instanceof Cache_Abstract) {
            if ($exceptions->throwExceptions()) {
                throw new Exception('Adapter class "' . $adapterName . '" does not extend Cache_Abstract.');
            } else {
                $exceptions->sendHttpStatus(500);
            }
        }

        return $cacheAdapter;
    }
}