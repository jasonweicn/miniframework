<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Db
{
    /**
     * 工厂模式获取数据库实例
     * 
     * @param string $adapter
     * @param array $params
     */
    public static function factory($adapter = 'Mysql', $params = array())
    {
        if (!is_array($params)) {
            throw new Exceptions('Adapter params must be in an array.');
        }
        
        if (!is_string($adapter) || empty($adapter)) {
            throw new Exceptions('Adapter name must be specified in a string.');
        }
        
        $adapterName = 'Db_' . ucwords($adapter);
        
        if (!class_exists($adapterName, false)) {
            $adapterPath = MINI_PATH . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'Db';
            $adapterFile = $adapterPath . DIRECTORY_SEPARATOR . $adapterName . '.php';
            if (!file_exists($adapterFile)) {
                throw new Exceptions('Adapter "' . $adapterName . '" not found.');
            }
            
            require_once($adapterFile);
        }
        
        $dbAdapter = new $adapterName($params);
        
        if (! $dbAdapter instanceof Db_Abstract) {
            throw new Exceptions('Adapter class "' . $adapterName . '" does not extend Db_Abstract.');
        }

        return $dbAdapter;
    }
}
