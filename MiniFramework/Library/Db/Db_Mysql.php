<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

namespace Mini\Db;

use \PDO;

class Db_Mysql extends Db_Abstract
{
    /**
     * 创建一个数据源
     * 
     */
    private function _dsn()
    {
        $dsn = array();
        
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
            throw new Exceptions('"dbname" must be in the params of Db.');
        }
        
        foreach ($dsn as $key => $val) {
            $dsn[$key] = "$key=$val";
        }

        return 'mysql:' . implode(';', $dsn);
    }
    
    /**
     * 创建一个数据库连接
     * 
     */
    protected function _connect()
    {
        if ($this->_dbh) return;
        
        if (!class_exists('PDO')) {
            throw new Exceptions('Not support PDO.');
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
            $this->_dbh = new PDO(
                $dsn,
                $this->_params['username'],
                $this->_params['passwd'],
                $this->_params['options']
            );
        } catch (Exceptions $e) {
            throw new Exceptions('Database connection failed.');
        }
        
        if (version_compare(PHP_VERSION, '5.3.6', '<') && !defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
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
        if ($this->_debug === true) $this->_debugSql($sql);
        try {
            $affected = $this->_dbh->exec($sql);
            if ($affected === false) {
                $this->_getPdoError();
            }
            return $affected;
        } catch (PDOException $e) {
            throw new Exceptions($e);
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
        if ($this->_debug === true) $this->_debugSql($sql);
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
        } catch (PDOException $e) {
            throw new Exceptions($e);
        }
    }
    
    /**
     * 插入记录
     *
     * @param string $table 表名
     * @param array $data 数据 array(col => value)
     * @return int
     */
    public function insert($table, array $data)
    {
        $sql = "INSERT INTO `$table` (`" . implode('`,`', array_keys($data)) . "`) VALUES ('" . implode("','", $data) . "')";
        
        return $this->execSql($sql);
    }
    
    /**
     * 批量插入记录
     * @param string $table
     * @param array $dataArray = array(
     *     0 => array(col1 => value1, col2 => value2),
     *     1 => array(col1 => value1, col2 => value2),
     *     ...
     * )
     * @return int
     */
    public function insertAll($table, array $dataArray)
    {
        $sql = "INSERT INTO `$table` (`" . implode('`,`', array_keys($dataArray[0])) . "`) VALUES ";
        
        foreach ($dataArray as $data) {
            $valSqls[] = "('" . implode("','", $data) . "')";
        }
        
        $sql .= implode(', ', $valSqls);
        
        return $this->execSql($sql);
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
        if (!empty($data)) {
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
        $flag = true;
        $this->_beginTransaction();
        foreach ($arraySql as $sql) {
            if ($this->execSql($sql) == 0) $flag = false;
        }
        if ($flag === false) {
            $this->_rollBack();
            return false;
        } else {
            $this->_commit();
            return true;
        }
    }
    
    /**
     * 获得最后一次插入记录的自增主键
     * 
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
            throw new Exceptions($errorInfo[2]);
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
