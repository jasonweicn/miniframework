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
        
        $adapterName = 'Db_' . ucwords($adapter);
        
        if (!class_exists($adapterName, false)) {
            $adapterPath = MINI_PATH . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'Db';
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
        
        $dbAdapter = new $adapterName($params);
        
        if (! $dbAdapter instanceof Db_Abstract) {
            if ($exceptions->throwExceptions()) {
                throw new Exception('Adapter class "' . $adapterName . '" does not extend Db_Abstract.');
            } else {
                $exceptions->sendHttpStatus(500);
            }
        }

        return $dbAdapter;
    }
}