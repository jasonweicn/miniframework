<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2018 http://www.sunbloger.com
// +---------------------------------------------------------------------------
// | Licensed under the Apache License, Version 2.0 (the "License");
// | you may not use this file except in compliance with the License.
// | You may obtain a copy of the License at
// |
// | http://www.apache.org/licenses/LICENSE-2.0
// |
// | Unless required by applicable law or agreed to in writing, software
// | distributed under the License is distributed on an "AS IS" BASIS,
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// | See the License for the specific language governing permissions and
// | limitations under the License.
// +---------------------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------
namespace Mini;

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
        if (! is_string($adapter) || empty($adapter)) {
            throw new Exception('Adapter name must be specified in a string.');
        }
        
        if (! in_array($adapter, array(
            'Mysql'
        ))) {
            throw new Exception('Adapter "' . $adapter . '" does not exist.');
        }
        
        if (! is_array($params)) {
            throw new Exception('Adapter params must be in an array.');
        }
        
        if (! isset($params['host'])) {
            throw new Exception('Database(' . $adapter . ') host is not defined.');
        } elseif (! isset($params['port'])) {
            throw new Exception('Database(' . $adapter . ') port is not defined.');
        } elseif (! isset($params['username'])) {
            throw new Exception('Database(' . $adapter . ') username is not defined.');
        } elseif (! isset($params['passwd'])) {
            throw new Exception('Database(' . $adapter . ') passwd is not defined.');
        } elseif (! isset($params['dbname'])) {
            throw new Exception('Database(' . $adapter . ') dbname is not defined.');
        }
        
        $adapterName = '\\Mini\\Db\\Db_' . ucwords($adapter);
        
        if (! class_exists($adapterName)) {
            throw new Exception('Adapter "' . $adapterName . '" not found.');
        }
        
        $dbAdapter = new $adapterName($params);
        
        if (! $dbAdapter instanceof \Mini\Db\Db_Abstract) {
            throw new Exception('Adapter class "' . $adapterName . '" does not extend Db_Abstract.');
        }
        
        return $dbAdapter;
    }
}
