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
     * 传入用于更新或插入的数据
     * 
     * @param array $data
     * @throws Exception
     * @return \Mini\Base\Model
     */
    public function data($data = null)
    {
        if ($data != null && is_array($data)) {
            $this->_options['data'] = $data;
        } else {
            throw new Exception('Data invalid.');
        }
        
        return $this;
    }
    
    /**
     * 添加数据
     * 
     * @throws Exception
     * @return int
     */
    public function add()
    {
        if (! isset($this->_options['data']) || empty($this->_options['data'])) {
            throw new Exception('Data invalid.');
        }
        if (! is_array($this->_options['data'])) {
            throw new Exception('Data is not array.');
        }
        
        if ($this->_curDb) {
            if (isIndexArray($this->_options['data'])) {
                $res = $this->_curDb->insertAll($this->getTable(), $this->_options['data']);
            } else {
                $res = $this->_curDb->insert($this->getTable(), $this->_options['data']);
            }
        } else {
            throw new Exception('Database is not found.');
        }
        $this->reset();
        
        return $res;
    }
    
    /**
     * 保存数据
     * 
     * @throws Exception
     * @return int
     */
    public function save()
    {
        if (! isset($this->_options['data']) || empty($this->_options['data'])) {
            throw new Exception('Data invalid.');
        }
        if (! is_array($this->_options['data'])) {
            throw new Exception('Data is not array.');
        }
        if (! isset($this->_options['where']) || $this->_options['where'] == '') {
            $where = '';
        } else {
            $where = $this->_options['where'];
        }
        
        if ($this->_curDb) {
            $res = $this->_curDb->update($this->getTable(), $this->_options['data'], $where);
        } else {
            throw new Exception('Database is not found.');
        }
        $this->reset();
        
        return $res;
    }
    
    public function delete()
    {
        if (! isset($this->_options['where']) || $this->_options['where'] == '') {
            $where = '';
        } else {
            $where = $this->_options['where'];
        }
        
        if ($this->_curDb) {
            $res = $this->_curDb->delete($this->getTable(), $where);
        } else {
            throw new Exception('Database is not found.');
        }
        $this->reset();
        
        return $res;
    }
    
    /**
     * 查询
     * 
     * @param string $type
     * @throws Exception
     * @return array
     */
    public function select($type = 'All')
    {
        $type = ($type == 'Row') ? 'Row' : 'All';
        $this->_method = 'SELECT';
        $sql = $this->createSql();
        $res = array();
        if ($this->_curDb) {
            $res = $this->_curDb->query($sql, $type);
        } else {
            throw new Exception('Database is not found.');
        }
        $this->reset();
        
        return $res;
    }
    
    /**
     * 设置查询字段
     * 
     * @param mixed $field string|array
     * @return \Mini\Base\Model
     */
    public function field($field = null)
    {
        if (isset($field)) {
            if (is_array($field)) {
                $field_text = '';
                foreach ($field as $val) {
                    $field_text .= '`' . $val . '`, ';
                }
                $this->_options['field'] = substr($field_text, 0, strlen($field_text) - 2);
            } else {
                $this->_options['field'] = trim($field);
            }
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
     * @param mixed $group
     * @return \Mini\Base\Model
     */
    public function group($group = null)
    {
        if (isset($group)) {
            if (is_array($group)) {
                $group_text = '';
                foreach ($group as $val) {
                    $group_text .= '`' . $val . '`, ';
                }
                $this->_options['group'] = substr($group_text, 0, strlen($group_text) - 2);
            } else {
                $this->_options['group'] = trim($group);
            }
        }
        
        return $this;
    }
    
    /**
     * 设置排序
     * 
     * @param mixed $order string|array
     * @return \Mini\Base\Model
     */
    public function order($order = null)
    {
        if (isset($order)) {
            if (is_array($order)) {
                $order_text = '';
                foreach ($order as $key => $val) {
                    if (! is_int($key) && (strtoupper($val) == 'ASC' || strtoupper($val) == 'DESC')) {
                        $order_text .= '`' . $key . '` ' . strtoupper($val) . ', ';
                    } else {
                        $order_text .= '`' . $val . '`, ';
                    }
                }
                $this->_options['order'] = substr($order_text, 0, strlen($order_text) - 2);
            } else {
                $this->_options['order'] = trim($order);
            }
        }
        
        return $this;
    }

    /**
     * LIMIT
     * 
     * @param int $param1
     * @param int $param2
     * @throws Exception
     * @return \Mini\Base\Model
     */
    public function limit($param1 = 1, $param2 = null)
    {
        if (isset($param2)) {
            
            list($param1, $param2) = array($param2, $param1);
            
            // param2 = offset
            if (is_int($param2)) {
                $this->_options['limit']['offset'] = $param2;
            } else {
                throw new Exception('Offset of limit invalid.');
            }
        }
        
        // param1 = rows
        if (! isset($param1) || empty($param1) || ! is_int($param1)) {
            throw new Exception('Rows of limit invalid.');
        }
        if ($param1 > 0) {
            $this->_options['limit']['rows'] = $param1;
        } else {
            throw new Exception('Rows of limit invalid.');
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
            $sql .= 'FROM `' . $this->_options['table'] . '`' . $where;
            if (isset($this->_options['group']) && $this->_options['group'] != '') {
                $sql .= ' GROUP BY ' . $this->_options['group'];
            }
            if (isset($this->_options['order']) && $this->_options['order'] != '') {
                $sql .= ' ORDER BY ' . $this->_options['order'];
            }
            if (isset($this->_options['limit']['rows'])) {
                if (isset($this->_options['limit']['offset'])) {
                    $sql .= ' LIMIT ' . $this->_options['limit']['offset']
                    . ', ' . $this->_options['limit']['rows'];
                } else {
                    $sql .= ' LIMIT ' . $this->_options['limit']['rows'];
                }
            }
        }
        
        return $sql;
    }
    
    /**
     * 获取当前操作的表
     * 
     * @throws Exception
     * @return string
     */
    private function getTable()
    {
        if (! isset($this->_options['table']) || $this->_options['table'] == '') {
            throw new Exception('Table invalid.');
        }
        
        return $this->_options['table'];
    }
    
    private function reset()
    {
        $this->_options = array();
        $this->_method = '';
        
        return true;
    }
}
