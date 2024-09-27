<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2024 http://www.sunbloger.com
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
// | Source: https://github.com/jasonweicn/miniframework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------
namespace Mini\Base;

use Mini\Db\Query;

class Model extends Query
{

    /**
     * 数据库对象池
     *
     * @var array
     */
    private $_dbPool;

    /**
     * 构造
     *
     * @return Action
     */
    function __construct()
    {
        if (DB_AUTO_CONNECT === true) {
            $this->_dbPool = App::getInstance()->getDbPool();
            $db = $this->loadDb('default');
            if ($db) {
                parent::__construct($db);
            }
        }
    }

    /**
     * 加载数据库对象
     *
     * @param string $key
     * @return NULL|object
     */
    public function loadDb(string $key)
    {
        if (! isset($this->_dbPool[$key])) {
            return null;
        }
        
        return $this->_dbPool[$key];
    }
    
    /**
     * 设置当前使用的数据库
     * 
     * @param string $key
     * @param array $params
     * @return boolean | $this
     */
    public function useDb(string $key, array $params = [])
    {
        $db = $this->loadDb($key);
        if ($db === null) {
            if (empty($params)) {
                throw new Exception('Failed to use the "' . $key . '" database object.');
            } else {
                $this->_curDb = $this->regDb($key, $params);
            }
        } else {
            $this->_curDb = $db;
        }
        
        return $this;
    }
    
    /**
     * 注册数据库对象
     * 
     * @param string $key
     * @param array $params
     * @return object
     */
    public function regDb(string $key, array $params)
    {
        if (isset($this->_dbPool[$key])) {
            throw new Exception('Failed to register database object, "' . $key . '" already exists.');
        }
        $this->_dbPool[$key] = \Mini\Db\Db::factory('Mysql', $params);
        
        return $this->_dbPool[$key];
    }
}
