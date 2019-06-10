<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2019 http://www.sunbloger.com
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

use Mini\Db;

abstract class Model
{

    /**
     * 数据库对象池
     *
     * @var object
     */
    protected $_dbPool;
    
    private $_curDb;
    
    private $_method;
    
    private $_options;

    /**
     * 构造
     *
     * @return Action
     */
    function __construct()
    {
        if (DB_AUTO_CONNECT === true) {
            $this->_dbPool = App::getInstance()->getDbPool();
        }
    }

    /**
     * 加载数据库对象
     *
     * @param string $key
     * @return NULL|object
     */
    public function loadDb($key)
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
     * @return bool | \Mini\Model
     */
    public function useDb($key, $params = null)
    {
        $db = $this->loadDb($key);
        if ($db == null) {
            if ($params != null) {
                $this->_curDb = $this->regDb($key, $params);
            } else {
                return false;
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
    public function regDb($key, $params)
    {
        $this->_dbPool[$key] = Db::factory('Mysql', $params);
        
        return $this->_dbPool[$key];
    }
    
    /**
     * 查询
     * 
     * @throws Exception
     * @return array
     */
    public function select()
    {
        $this->_method = 'SELECT';
        $sql = $this->createSql();
        if ($this->_curDb) {
            $res = $this->_curDb->query($sql, 'All');
            return $res;
        } else {
            throw new Exception('Database is not found.');
        }
    }
    
    /**
     * 设置查询字段
     * 
     * @param string $field
     * @return \Mini\Base\Model
     */
    public function field($field = null)
    {
        if ($field != null) {
            $this->_options['field'] = trim($field);
        }
        
        return $this;
    }
    
    /**
     * 设置数据表
     * 
     * @param string $table
     * @return \Mini\Base\Model
     */
    public function table($table = null)
    {
        if ($table != null) {
            $this->_options['table'] = trim($table);
        }
        
        return $this;
    }
    
    /**
     * 设置查询条件
     * 
     * @param string $where
     * @return \Mini\Base\Model
     */
    public function where($where = null)
    {
        if ($where != null) {
            $this->_options['where'] = trim($where);
        }
        
        return $this;
    }
    
    /**
     * 设置分组
     * 
     * @param string $group
     * @return \Mini\Base\Model
     */
    public function group($group = null)
    {
        if ($group != null) {
            $this->_options['group'] = trim($group);
        }
        
        return $this;
    }
    
    /**
     * 设置排序
     * 
     * @param string $order
     * @return \Mini\Base\Model
     */
    public function order($order = null)
    {
        if ($order != null) {
            $this->_options['order'] = trim($order);
        }
        
        return $this;
    }
    
    /**
     * 设置LIMIT
     * 
     * @param string $limit
     * @return \Mini\Base\Model
     */
    public function limit($limit = null)
    {
        if ($limit != null) {
            $this->_options['limit'] = trim($limit);
        }
        
        return $this;
    }
    
    /**
     * 创建SQL语句
     * 
     * @return bool | string
     */
    private function createSql()
    {
        if (! isset($this->_options['table']) || $this->_options['table'] == '') {
            return false;
        }
        
        $sql = $this->_method;
        
        if (! isset($this->_options['where']) || $this->_options['where'] == '') {
            $where = '';
        } else {
            $where = ' WHERE ' . $this->_options['where'];
        }
        
        if ($this->_method == 'SELECT') {
            
            if (! isset($this->_options['field']) || $this->_options['field'] == '') {
                $sql .= ' * ';
            } else {
                $sql .= ' ' . $this->_options['field'] . ' ';
            }
            $sql .= 'FROM ' . $this->_options['table'] . $where;
            if (isset($this->_options['group']) && $this->_options['group'] != '') {
                $sql .= ' GROUP BY ' . $this->_options['group'];
            }
            if (isset($this->_options['order']) && $this->_options['order'] != '') {
                $sql .= ' ORDER BY ' . $this->_options['order'];
            }
            if (isset($this->_options['limit']) && $this->_options['limit'] != '') {
                $sql .= ' LIMIT ' . $this->_options['limit'];
            }
            
        }
        
        $this->_options = array();
        
        $this->_method = '';
        
        return $sql;
    }
}
