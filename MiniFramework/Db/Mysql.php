<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2021 http://www.sunbloger.com
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
use PDO;

class Mysql extends Db_Abstract
{

    /**
     * 创建一个数据源
     */
    private function _dsn()
    {
        $dsn = [];

        if (isset($this->_params['host']) && is_string($this->_params['host'])) {
            $dsn['host'] = $this->_params['host'];
        } else {
            $dsn['host'] = 'localhost';
        }

        if (isset($this->_params['port'])) {
            $dsn['port'] = $this->_params['port'];
        }

        if (isset($this->_params['dbname']) && is_string($this->_params['dbname'])) {
            $dsn['dbname'] = $this->_params['dbname'];
        } else {
            throw new Exception('"dbname" must be in the params of Db.');
        }

        foreach ($dsn as $key => $val) {
            $dsn[$key] = "$key=$val";
        }

        return 'mysql:' . implode(';', $dsn);
    }

    /**
     * 创建一个数据库连接
     */
    protected function _connect()
    {
        if ($this->_dbh)
            return;

        if (! class_exists('PDO')) {
            throw new Exception('Not support PDO.');
        }

        $dsn = $this->_dsn();

        $this->_params['options'][PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

        if (version_compare(PHP_VERSION, '5.3.6', '>=')) {
            $dsn .= ';charset=' . $this->_params['charset'];
        } else {
            if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
                $this->_params['options'][PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $this->_params['charset'];
            }
        }

        if (isset($this->_params['persistent']) && ($this->_params['persistent'] == true)) {
            $this->_params['options'][PDO::ATTR_PERSISTENT] = true;
        } else {
            $this->_params['options'][PDO::ATTR_PERSISTENT] = false;
        }

        try {
            $this->_dbh = new PDO($dsn, $this->_params['username'], $this->_params['passwd'], $this->_params['options']);
        } catch (Exception $e) {
            throw new Exception('Database connection failed.');
        }

        if (version_compare(PHP_VERSION, '5.3.6', '<') && ! defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
            $this->_dbh->exec('SET NAMES ' . $this->_params['charset']);
        }
    }

    /**
     * 执行SQL语句
     *
     * @param string $sql
     * @return int
     */
    public function execSql($sql = null)
    {
        $this->_connect();
        $this->_setLastSql($sql);
        if ($this->_debug === true)
            $this->_debugSql($sql);
        try {
            $affected = $this->_dbh->exec($sql);
            if ($affected === false) {
                $this->_getPdoError();
            }
            return $affected;
        } catch (\PDOException $e) {
            throw new Exception($e);
        }
    }

    /**
     * 查询SQL语句
     *
     * @param string $sql SQL语句
     * @param string $queryMode 查询方式(All or Row)
     * @return array
     */
    public function query($sql = null, $queryMode = 'All')
    {
        $this->_connect();
        $this->_setLastSql($sql);
        if ($this->_debug === true)
            $this->_debugSql($sql);
        try {
            $recordset = $this->_dbh->query($sql);
            if ($recordset === false) {
                $this->_getPdoError();
            }
            $recordset->setFetchMode(PDO::FETCH_ASSOC);

            if ($queryMode == 'All') {
                $result = $recordset->fetchAll();
            } elseif ($queryMode == 'Row') {
                $result = $recordset->fetch();
            } else {
                $result = null;
            }

            return $result;
        } catch (\PDOException $e) {
            throw new Exception($e);
        }
    }

    /**
     * 插入记录
     *
     * @param string $table 表名
     * @param array $data 数据 array(col => value)
     * @param boolean $prepare 是否进行预处理
     * @return int
     */
    public function insert($table, array $data, $prepare = true)
    {
        if ($prepare === true) {
            $result = $this->prepareInsert($table, $data);
        } else {
            $result = $this->execSql("INSERT INTO `$table` (`" . implode('`,`', array_keys($data)) . "`) VALUES ('" . implode("','", $data) . "')");;
        }
        
        return $result;
    }

    /**
     * 预处理方式插入记录
     *
     * @param string $table 表名
     * @param array $data 数据 array(col => value)
     * @return boolean
     */
    public function prepareInsert($table, array $data)
    {
        $this->_connect();
        if (empty($data)) {
            return false;
        }
        $prepareParams = [];
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                throw new Exception('Value cannot be an array.');
                return false;
            }
            $prepareParams[':' . $k] = $v;
        }
        try {
            $sql = "INSERT INTO `$table` (`" . implode('`, `', array_keys($data)) . "`) VALUES (" . implode(', ', array_keys($prepareParams)) . ")";
            $stmt = $this->_dbh->prepare($sql);
            $res = $stmt->execute($prepareParams);
            return $res;
        } catch (\PDOException $e) {
            throw new Exception($e);
        }

        return false;
    }

    /**
     * 批量插入记录
     *
     * @param string $table
     * @param array $dataArray = array(
     *        0 => array(col1 => value1, col2 => value2),
     *        1 => array(col1 => value1, col2 => value2),
     *        ...
     *        )
     * @param boolean $prepare 是否进行预处理
     * @return int
     */
    public function insertAll($table, array $dataArray, $prepare = true)
    {
        if ($prepare === true) {
            $result = $this->prepareInsertAll($table, $dataArray);
        } else {
            $sql = "INSERT INTO `$table` (`" . implode('`,`', array_keys($dataArray[0])) . "`) VALUES ";
            $valSqls = [];
            foreach ($dataArray as $data) {
                $valSqls[] = "('" . implode("','", $data) . "')";
            }
            $sql .= implode(', ', $valSqls);
            $result = $this->execSql($sql);
        }
        
        return $result;
    }
    
    /**
     * 预处理方式批量插入记录
     *
     * @param string $table
     * @param array $dataArray = array(
     *        0 => array(col1 => value1, col2 => value2),
     *        1 => array(col1 => value1, col2 => value2),
     *        ...
     *        )
     * @return boolean
     */
    public function prepareInsertAll($table, array $dataArray)
    {
        $this->_connect();
        if (empty($dataArray)) {
            return false;
        }
        $prepareParams = [];
        $prepareData = [];
        foreach ($dataArray as $key => $data) {
            if (empty($data)) {
                throw new Exception('Incorrect data format.');
                return false;
            }
            if (! is_array($data)) {
                throw new Exception('Incorrect data format.');
                return false;
            }
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    throw new Exception('Value cannot be an array.');
                    return false;
                }
                $prepareParams[$key][] = ':' . $k . $key;
                $prepareData[':' . $k . $key] = $v;
            }
        }
        try {
            $sql = "INSERT INTO `$table` (`" . implode('`, `', array_keys($dataArray[0])) . "`) VALUES ";
            $valSqls = [];
            foreach ($prepareParams as $curParams) {
                $valSqls[] = "(" . implode(', ', $curParams) . ")";
            }            
            $sql .= implode(', ', $valSqls);
            $stmt = $this->_dbh->prepare($sql);
            $res = $stmt->execute($prepareData);
            return $res;
        } catch (\PDOException $e) {
            throw new Exception($e);
        }

        return false;
    }

    /**
     * 更新记录
     *
     * @param string $table 表名
     * @param array $data 数据 array(col => value)
     * @param string $where 条件
     * @return int
     */
    public function update($table, array $data, $where = '')
    {
        $sql = '';
        if (! empty($data)) {
            foreach ($data as $key => $value) {
                $sql .= ", `$key`='$value'";
            }
        }
        $sql = substr($sql, 1);
        $sql = "UPDATE `$table` SET $sql" . (($where) ? " WHERE $where" : '');

        return $this->execSql($sql);
    }

    /**
     * 替换记录
     *
     * @param string $table 表名
     * @param array $data 数据 array(col => value)
     * @return int
     */
    public function replace($table, array $data)
    {
        $sql = "REPLACE INTO `$table`(`" . implode('`,`', array_keys($data)) . "`) VALUES ('" . implode("','", $data) . "')";

        return $this->execSql($sql);
    }

    /**
     * 删除记录
     *
     * @param string $table 表名
     * @param string $where 条件
     * @return int
     */
    public function delete($table, $where = '')
    {
        $sql = "DELETE FROM `$table`" . (($where) ? " WHERE $where" : '');
        return $this->execSql($sql);
    }

    /**
     * 按指定条件查询行数
     *
     * @param string $table
     * @param string $col
     * @param string $where
     * @return int
     */
    public function countRow($table, $col = '*', $where = '')
    {
        $sql = "SELECT COUNT($col) AS rows FROM `$table`" . (($where) ? " WHERE $where" : '');
        $result = $this->query($sql, 'Row');
        return $result['rows'];
    }

    /**
     * 获取字段最大值
     *
     * @param string $table 表名
     * @param string $col 字段名
     * @param string $where 条件
     */
    public function getMaxValue($table, $col, $where = '')
    {
        $sql = "SELECT MAX($col) AS max_value FROM `$table`" . (($where) ? " WHERE $where" : '');
        $result = $this->query($sql, 'Row');
        $maxValue = $result["max_value"];
        if ($maxValue == "" || $maxValue == null) {
            $maxValue = 0;
        }
        return $maxValue;
    }
    
    /**
     * 检查数据表是否存在
     * 
     * @param string $table
     * @param string $dbname
     * @return boolean
     */
    public function checkTableIsExist($table, $dbname = null)
    {
        if ($dbname === null) {
            $dbname = $this->_params['dbname'];
        }
        $sql = "SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA='" . $dbname . "' AND TABLE_NAME = '" . $table . "'";
        $result = $this->query($sql, 'Row');
        if (! empty($result)) {
            if ($result['TABLE_NAME'] == $table) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 获取表引擎
     *
     * @param string $table 表名
     * @return string
     */
    public function getTableEngine($table)
    {
        $sql = "SHOW TABLE STATUS FROM `" . $this->_params['dbname'] . "` WHERE `Name`='" . $table . "'";
        $result = $this->query($sql);
        return $result[0]['Engine'];
    }

    /**
     * 事务开始
     */
    protected function _beginTransaction()
    {
        $this->_dbh->beginTransaction();
    }

    /**
     * 事务提交
     */
    protected function _commit()
    {
        $this->_dbh->commit();
    }

    /**
     * 事务回滚
     */
    protected function _rollBack()
    {
        $this->_dbh->rollBack();
    }

    /**
     * 通过事务处理多条SQL语句
     *
     * @param array $arraySql
     * @return boolean
     */
    public function execTrans(array $arraySql)
    {
        try {
            $this->_connect();
            $this->_dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
            $this->_beginTransaction();
            foreach ($arraySql as $sql) {
                $this->execSql($sql);
            }
            $this->_commit();
            $this->_dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            return true;
        } catch (\PDOException $e) {
            $this->_rollBack();
            throw new Exception($e);
        }
    }

    /**
     * 获得最后一次插入记录的自增主键
     */
    public function lastInsertId()
    {
        $this->_connect();
        return $this->_dbh->lastInsertId();
    }

    /**
     * 捕获PDO错误信息
     */
    private function _getPdoError()
    {
        $this->_connect();
        if ($this->_dbh->errorCode() != '00000') {
            $errorInfo = $this->_dbh->errorInfo();
            throw new Exception($errorInfo[2]);
        }
    }

    /**
     * 关闭数据库连接
     */
    public function close()
    {
        $this->_dbh = null;
    }
}
