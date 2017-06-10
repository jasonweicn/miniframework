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
        if (!is_string($adapter) || empty($adapter)) {
            throw new Exceptions('Adapter name must be specified in a string.');
        }
        
        if (!in_array($adapter, array('Mysql'))) {
            throw new Exceptions('Adapter "' . $adapter . '" does not exist.');
        }
        
        if (!is_array($params)) {
            throw new Exceptions('Adapter params must be in an array.');
        }
        
        if (!isset($params['host'])) {
            throw new Exceptions('Database(' . $adapter . ') host is not defined.');
        } elseif (!isset($params['port'])) {
            throw new Exceptions('Database(' . $adapter . ') port is not defined.');
        } elseif (!isset($params['username'])) {
            throw new Exceptions('Database(' . $adapter . ') username is not defined.');
        } elseif (!isset($params['passwd'])) {
            throw new Exceptions('Database(' . $adapter . ') passwd is not defined.');
        } elseif (!isset($params['dbname'])) {
            throw new Exceptions('Database(' . $adapter . ') dbname is not defined.');
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
