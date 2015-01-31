<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

abstract class Db_Abstract
{
    /**
     * 数据库连接
     * 
     * @var object | resource | null
     */
    protected $_dbh = null;
    
    /**
     * 数据库连接参数
     * 
     * @var array
     */
    protected $_params = array();
    
    /**
     * 最后一次执行的SQL语句
     * 
     * @var string
     */
    protected $_lastSql = null;
    
    /**
     * 创建一个数据库连接
     * 
     */
    abstract protected function _connect();
        
    /**
     * 关闭数据库连接
     * 
     */
    abstract protected function close();
    
    /**
     * 执行SQL语句
     * 
     * @param string $sql
     */
    abstract protected function execSql($sql = null);
    
    /**
     * 查询SQL语句
     * 
     * @param mixed $sql
     * @param mixed $queryMode
     */
    abstract protected function query($sql = null, $queryMode = 'All');
    
    /**
     * 插入记录
     * 
     * @param string $table
     * @param array $data
     */
    abstract protected function insert($table, array $data);
    
    /**
     * 更新记录
     * 
     * @param string $table
     * @param array $data
     * @param string $where
     */
    abstract protected function update($table, array $data, $where = '');
    
    /**
     * 替换记录
     * 
     * @param string $table
     * @param array $data
     */
    abstract protected function replace($table, array $data);
    
    /**
     * 删除记录
     * 
     * @param string $table
     * @param string $where
     */
    abstract protected function delete($table, $where = '');
    
    /**
     * 按指定条件查询行数
     * 
     * @param string $table
     * @param string $col
     * @param string $where
     */
    abstract protected function countRow($table, $col = '*', $where = '');
    
    /**
     * 构造
     * 
     * @param array $params => array (
     *     host         => (string) 主机，默认值为localhost
     *     dbname       => (string) 数据库名
     *     username     => (string) 用户名
     *     passwd       => (string) 密码
     * 
     *     port         => (string) 端口
     *     charset      => (string) 字符集编码，默认值为utf8
     *     persistent   => (boolean) 是否启用持久连接，默认值为false
     * )
     * @return Db_Abstract
     */
    public function __construct($params)
    {
        if (!is_array($params)) {
            throw new Exception('Adapter params must be in an array.');
        }
        
        if (!isset($params['charset'])) {
            $params['charset'] = 'utf8';
        }
        
        if (!isset($params['persistent'])) {
            $params['persistent'] = false;
        }
        
        $this->_params = $params;
    }
    
    /**
     * 事务开始（抽象）
     * 
     */
    abstract protected function _beginTransaction();
    
    /**
     * 事务开始
     * 
     */
    public function beginTransaction()
    {
        $this->_beginTransaction();
    }
    
    /**
     * 事务提交（抽象）
     * 
     */
    abstract protected function _commit();
    
    /**
     * 事务提交
     */
    public function commit()
    {
        $this->_commit();
    }
    
    /**
     * 事务回滚（抽象）
     * 
     */
    abstract protected function _rollBack();
    
    /**
     * 事务回滚
     */
    public function rollBack()
    {
        $this->_rollBack();
    }
    
    /**
     * 获取最后一次执行的SQL语句
     * 
     */
    public function getLastSql ()
    {
        return $this->_lastSql;
    }
    
    /**
     * 保存最后一次执行的SQL语句（抽象）
     * 
     * @param string $sql
     */
    abstract protected function _setLastSql($sql = null);
}