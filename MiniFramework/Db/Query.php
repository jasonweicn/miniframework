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
namespace Mini\Db;

use Mini\Base\Exception;

class Query
{

    /**
     * 数据库对象池
     *
     * @var object
     */
    //protected $_dbPool;
    
    /**
     * 当前数据对象
     * 
     * @var object
     */
    protected $_curDb;
    
    private $_method;
    
    private $_options;
    
    private $_distinct = false;
    
    private $_debugSql = false;
    
    /**
     * 比较运算符
     * 
     * @var array
     */
    private $_compareSymbol = [
        '=',
        '>',
        '<',
        '>=',
        '>=',
        '<>',
        '!=',
        'IN',
        'NOT IN',
        'BETWEEN',
        'NOT BETWEEN',
        'LIKE',
        'IS NULL',
        'IS NOT NULL'
    ];
    
    /**
     * 逻辑运算符
     * 
     * @var array
     */
    private $_logicSymbol = [
        'AND',
        'OR'
    ];

    function __construct($db)
    {
        $this->_curDb = $db;
    }

    /**
     * 传入用于更新或插入的数据
     * 
     * @param array $data
     * @return $this
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
     * @param bool $prepare
     * @return int
     */
    public function add(bool $prepare = true)
    {
        if (! isset($this->_options['data']) || empty($this->_options['data'])) {
            throw new Exception('Data invalid.');
        }
        if (! is_array($this->_options['data'])) {
            throw new Exception('Data is not array.');
        }
        
        if ($this->_curDb) {
            if ($this->_debugSql === true) {
                $this->_curDb->debug();
            }
            if (isIndexArray($this->_options['data'])) {
                $res = $this->_curDb->insertAll($this->getTable(), $this->_options['data'], $prepare);
            } else {
                $res = $this->_curDb->insert($this->getTable(), $this->_options['data'], $prepare);
            }
        } else {
            throw new Exception('Database object is not found.');
        }
        $this->reset();
        
        return $res;
    }
    
    /**
     * 保存数据
     * 
     * @param bool $prepare
     * @return int
     */
    public function save(bool $prepare = true)
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
            if ($this->_debugSql === true) {
                $this->_curDb->debug();
            }
            $res = $this->_curDb->update($this->getTable(), $this->_options['data'], $where, $prepare);
        } else {
            throw new Exception('Database object is not found.');
        }
        $this->reset();
        
        return $res;
    }
    
    /**
     * 删除
     * 
     * @throws Exception
     * @return mixed
     */
    public function delete()
    {
        if (! isset($this->_options['where']) || $this->_options['where'] == '') {
            $where = '';
        } else {
            $where = $this->_options['where'];
        }
        
        if ($this->_curDb) {
            if ($this->_debugSql === true) {
                $this->_curDb->debug();
            }
            $res = $this->_curDb->delete($this->getTable(), $where);
        } else {
            throw new Exception('Database object is not found.');
        }
        $this->reset();
        
        return $res;
    }
    
    /**
     * 查询
     * 
     * @param string $type
     * @return array
     */
    public function select($type = 'all')
    {
        $type = strtolower($type);
        $type = ($type == 'one' || $type == 'row') ? 'row' : 'all';
        $this->_method = 'SELECT';
        $sql = $this->createSql();
        if ($this->_curDb) {
            if ($this->_debugSql === true) {
                $this->_curDb->debug();
            }
            $res = $this->_curDb->query($sql, $type);
        } else {
            throw new Exception('Database object is not found.');
        }
        $this->reset();
        
        return $res;
    }
    
    /**
     * 查询返回一行
     * 
     * @return array
     */
    public function selectRow()
    {
        return $this->select('row');
    }
    
    /**
     * 查询返回所有行
     * 
     * @return array
     */
    public function selectAll()
    {
        return $this->select('all');
    }

    /**
     * 设置 DISTINCT 去重
     * 
     * @return $this
     */
    public function distinct()
    {
        $this->_options['distinct'] = true;
        
        return $this;
    }

    /**
     * 设置查询字段
     * 
     * @param mixed $field string|array
     * @return $this
     */
    public function field($field = null)
    {
        if (isset($field)) {
            if (is_array($field)) {
                $fieldString = '';
                foreach ($field as $key => $val) {
                    $curFieldString = $val;
                    if (! is_int($key)) {
                        $curFieldString = $key . ' AS ' . $val;
                    }
                    $fieldString .= $curFieldString . ', ';
                }
                $this->_options['field'] = substr($fieldString, 0, strlen($fieldString) - 2);
            } else {
                $this->_options['field'] = trim(str_replace([' as ', ' As ', ' aS '], ' AS ', $field));
            }
        }
        
        return $this;
    }
    
    /**
     * 设置 FROM 数据表
     * 
     * @param string $table
     * @return $this
     */
    public function table($table = null)
    {
        if ($table === null) {
            throw new Exception('Param invalid.');
        }
        if (is_array($table)) {
            $tableString = '';
            foreach ($table as $key => $val) {
                $curTableString = $val;
                if (! is_int($key)) {
                    $curTableString = $key . ' AS ' . $val;
                }
                $tableString .= $curTableString . ', ';
            }
            $this->_options['table'] = substr($tableString, 0, strlen($tableString) - 2);
        } else {
            $this->_options['table'] = trim(str_replace([' as ', ' As ', ' aS '], ' AS ', $table));
        }
        
        return $this;
    }
    
    /**
     * 设置 FROM 数据表（为符合使用习惯，封装了 table 方法）
     * 
     * @param string $table
     * @return $this
     */
    public function from($table = null)
    {
        return $this->table($table);
    }
    
    /**
     * 联表查询（默认INNER方式）
     * 
     * @param string $table
     * @param string $condition
     * @param string $type (default: INNER | LEFT | RIGHT)
     * @return $this
     */
    public function join(string $table = null, string $condition = null, string $type = 'INNER')
    {
        if ($table == null || $condition == null || $type == null) {
            throw new Exception('Param invalid.');
        }
        $type = strtoupper($type);
        if ($type != 'LEFT' && $type != 'INNER' && $type != 'RIGHT') {
            throw new Exception('Param invalid.');
        }
        switch (strtoupper($type)) {
            case 'LEFT':
                return $this->leftjoin($table, $condition);
                break;
            case 'RIGHT':
                return $this->rightjoin($table, $condition);
                break;
            case 'INNER':
            default:
                return $this->innerjoin($table, $condition);
                break;
        }
        
        return $this;
    }
    
    /**
     * 联表查询（INNER JOIN）
     * 
     * @param string $table
     * @param string $condition
     * @return $this
     */
    public function innerjoin(string $table = null, string $condition = null)
    {
        if ($table == null || $condition == null) {
            throw new Exception('Param invalid.');
        }
        $this->_options['join'][] = 'INNER JOIN ' . $table . ' ON ' . $condition;
        
        return $this;
    }
    
    /**
     * 联表查询（LEFT JOIN）
     * 
     * @param string $table
     * @param string $condition
     * @return $this
     */
    public function leftjoin(string $table = null, string $condition = null)
    {
        if ($table == null || $condition == null) {
            throw new Exception('Param invalid.');
        }
        $this->_options['join'][] = 'LEFT JOIN ' . $table . ' ON ' . $condition;
        
        return $this;
    }
    
    /**
     * 联表查询（RIGHT JOIN）
     * 
     * @param string $table
     * @param string $condition
     * @return $this
     */
    public function rightjoin(string $table = null, string $condition = null)
    {
        if ($table == null || $condition == null) {
            throw new Exception('Param invalid.');
        }
        $this->_options['join'][] = 'RIGHT JOIN ' . $table . ' ON ' . $condition;
        
        return $this;
    }
    
    /**
     * 设置 WHERE 查询条件
     * 
     * @param mixed $defaultParam 通常为查询条件字符串，也可作为要查询的字段名和其他参数组合使用
     * @return $this
     */
    public function where($defaultParam = null)
    {
        $paramsNum = func_num_args();
        $params = func_get_args();
        
        if ($paramsNum == 1) {
            // 只传入一个参数时，默认为兼容旧版本的调用方式
            if ($defaultParam == null) {
                throw new Exception('Param invalid.');
            }
            $this->_options['where'] = trim($defaultParam);
        } elseif ($paramsNum == 2) {
            if ($params[0] == null) {
                throw new Exception('Param invalid.');
            }
            // [key1=>value1, key2=>value2], AND|OR
            if (is_array($params[0]) && in_array($params[1], $this->_logicSymbol)) {
                $this->_options['where'] = $this->getWhereByMultiFields($params[0], $params[1]);
            } else {
                if (! is_array($params[1])) {
                    $this->_options['where'] = $this->getWhereString($params[0], [$params[1]]);
                } else {
                    $this->_options['where'] = $this->getWhereString($params[0], $params[1], '=', 'OR');
                }
            }
        } elseif ($paramsNum == 3) {
            if ($params[0] == null) {
                throw new Exception('Param invalid.');
            }
            if (! is_array($params[1])) {
                if (! in_array(strtoupper($params[1]), $this->_compareSymbol)) {
                    throw new Exception('Param invalid.');
                }
                if (! is_array($params[2])) {
                    $this->_options['where'] = $this->getWhereString($params[0], [$params[2]], strtoupper($params[1]));
                } else {
                    $this->_options['where'] = $this->getWhereString($params[0], $params[2], strtoupper($params[1]), 'OR');
                }
            } else {
                if (! in_array(strtoupper($params[2]), $this->_logicSymbol)) {
                    throw new Exception('Param invalid.');
                }
                $this->_options['where'] = $this->getWhereString($params[0], $params[1], '=', strtoupper($params[2]));
            }
        } elseif ($paramsNum == 4) {
            if ($params[3] == null) {
                throw new Exception('Param invalid.');
            }
            $this->_options['where'] = $this->getWhereString($params[0], $params[2], $params[1], $params[3]);
        } else {
            throw new Exception('Param invalid.');
        }
        
        return $this;
    }
    
    /**
     * 构造查询语句字符串
     * 
     * @param string $field
     * @param mixed $values
     * @param string $compareSymbol
     * @param string $logicSymbol
     * @return string
     */
    private function getWhereString($field, $values, $compareSymbol = '=', $logicSymbol = null)
    {
        if (! in_array($compareSymbol, $this->_compareSymbol)) {
            throw new Exception('Compare symbol invalid.');
        }
        if ($logicSymbol != null) {
            if (! in_array($logicSymbol, $this->_logicSymbol)) {
                throw new Exception('Logic symbol invalid.');
            }
        }
        
        if ($compareSymbol == 'IN' || $compareSymbol == 'NOT IN') {
            $valueArray = [];
            foreach ($values as $value) {
                $valueArray[] = $value == null ? 'NULL' : "'" . trim($value) . "'";
            }
            $whereString = '`' . trim($field) . "` " . $compareSymbol . " (" . implode(', ', $valueArray) . ")";
        } else {
            $whereArray = [];
            foreach ($values as $value) {
                $value = $value == null ? 'NULL' : trim($value);
                $whereArray[] = '`' . trim($field) . "` " . $compareSymbol . " '" . $value . "'";
            }
            $whereString = implode(' ' . $logicSymbol . ' ', $whereArray);
        }
        
        return $whereString;
    }
    
    private function getWhereByMultiFields($conditions, $logicSymbol = 'AND')
    {
        if (! is_array($conditions)) {
            throw new Exception('Invalid query conditions.');
        }
        if (! in_array($logicSymbol, $this->_logicSymbol)) {
            throw new Exception('Invalid query logic symbol.');
        }
        foreach ($conditions as $field => $value) {
            $indexArray[] = $field . '="' . $value . '"';
        }
        $where = implode(' ' . $logicSymbol . ' ', $indexArray);
        
        return $where;
    }
    
    /**
     * 设置 GROUP BY 分组
     * 
     * @param mixed $group
     * @return $this
     */
    public function group($group = null)
    {
        if (isset($group)) {
            if (is_array($group)) {
                $groupString = '';
                foreach ($group as $val) {
                    $groupString .= $val . ', ';
                }
                $this->_options['group'] = substr($groupString, 0, strlen($groupString) - 2);
            } else {
                $this->_options['group'] = trim($group);
            }
        }
        
        return $this;
    }

    /**
     * 设置 HAVING 过滤（仅在 GROUP BY 时生效）
     * 
     * @param string $condition
     * @return $this
     */
    public function having($condition)
    {
        $this->_options['having'] = $condition;
        
        return $this;
    }

    /**
     * 设置 ORDER BY
     * 
     * @param mixed $order string|array
     * @return $this
     */
    public function order($order = null)
    {
        if (isset($order)) {
            if (is_array($order)) {
                $orderString = '';
                foreach ($order as $key => $val) {
                    if (! is_int($key) && (strtoupper($val) == 'ASC' || strtoupper($val) == 'DESC')) {
                        $orderString .= $key . ' ' . strtoupper($val) . ', ';
                    } else {
                        $orderString .= $val . ', ';
                    }
                }
                $this->_options['order'] = substr($orderString, 0, strlen($orderString) - 2);
            } else {
                $this->_options['order'] = trim($order);
            }
        }
        
        return $this;
    }

    /**
     * 设置 LIMIT
     * 
     * @param int $param1 rows or offset (default:1)
     * @param int $param2 rows
     * @return $this
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
     * 设置分页
     * 
     * @param int $page
     * @param int $size (default: 10)
     * @return $this
     */
    public function page(int $page, int $size = 10)
    {
        $page = empty($page) ? 1 : $page;
        $size = empty($size) ? 10 : $size;
        $offset = $page == 1 ? 0 : $size * ($page - 1);
        $this->limit($offset, $size);
        
        return $this;
    }

    /**
     * 创建SQL语句
     * 
     * @return boolean|string
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
            if (isset($this->_options['distinct']) && $this->_options['distinct'] === true) {
                $sql .= ' DISTINCT';
            }
            if (! isset($this->_options['field']) || $this->_options['field'] == '') {
                $sql .= ' * ';
            } else {
                $sql .= ' ' . $this->_options['field'] . ' ';
            }
            
            // FROM
            $sql .= 'FROM ' . $this->_options['table'];
            
            // JOIN
            if (isset($this->_options['join'])) {
                $sql .= ' ' . implode(' ', $this->_options['join']);
            }
            
            // WHERE
            $sql .= $where;
            
            // GROUP BY
            if (isset($this->_options['group']) && $this->_options['group'] != '') {
                $sql .= ' GROUP BY ' . $this->_options['group'];
                // HAVING
                if (isset($this->_options['having']) && ! empty($this->_options['having'])) {
                    $sql .= ' HAVING ' . $this->_options['having'];
                }
            }
            
            // ORDER BY
            if (isset($this->_options['order']) && $this->_options['order'] != '') {
                $sql .= ' ORDER BY ' . $this->_options['order'];
            }
            
            // LIMIT
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
     * 获取最近一条SQL语句
     * 
     * @return string
     */
    public function getLastSql()
    {
        return $this->_curDb->getLastSql();
    }

    /**
     * 事务开始
     * 
     * @return \Mini\Base\Model
     */
    public function beginTransaction()
    {
        $this->_curDb->beginTransaction();
        
        return $this;
    }

    /**
     * 事务提交
     * 
     * @return $this
     */
    public function commit()
    {
        $this->_curDb->commit();
        
        return $this;
    }

    /**
     * 事务回滚
     * 
     * @return $this
     */
    public function rollBack()
    {
        $this->_curDb->rollBack();
        
        return $this;
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
    
    /**
     * 重置
     * 
     * @return boolean
     */
    private function reset()
    {
        $this->_options = [];
        $this->_method = '';
        
        return true;
    }
    
    /**
     * 设置开启DEBUG模式，输出显示 SQL 语句
     * 
     * @return $this
     */
    public function debug()
    {
        $this->_debugSql = true;
        
        return $this;
    }
}
