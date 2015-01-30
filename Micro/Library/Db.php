<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
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
            throw new Exception('Adapter params must be in an array.');
        }
        
        if (!is_string($adapter) || empty($adapter)) {
            throw new Exception('Adapter name must be specified in a string.');
        }
        
        $adapterName = 'Db_' . ucwords($adapter);
        
        if (!class_exists($adapterName, false)) {
            $adapterPath = MICRO_PATH . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'Db';
            require_once $adapterPath . DIRECTORY_SEPARATOR . $adapterName . '.php';
        }
        
        $dbAdapter = new $adapterName($params);
        
        if (! $dbAdapter instanceof Db_Abstract) {
            throw new Exception('Adapter class "' . $adapterName . '" does not extend Db_Abstract.');
        }

        return $dbAdapter;
    }
}