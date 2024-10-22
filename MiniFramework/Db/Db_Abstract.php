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
    protected $_params = [];

    /**
     * 最后一次执行的SQL语句
     *
     * @var string
     */
    protected $_lastSql = null;

    /**
     * 调试
     * 
     * @var boolean
     */
    protected $_debug = false;

    /**
     * 创建一个数据库连接
     */
    abstract protected function _connect();

    /**
     * 关闭数据库连接
     */
    abstract protected function close();

    /**
     * 执行SQL语句
     *
     * @param string $sql
     */
    abstract protected function execSql($sql);

    /**
     * 查询SQL语句
     *
     * @param string $sql            
     * @param string $queryMode
     * @param array $binds
     */
    abstract protected function query($sql, $queryMode = 'all', $binds = []);

    /**
     * 插入记录
     *
     * @param string $table            
     * @param array $data    
     * @param boolean $prepare        
     */
    abstract protected function insert($table, array $data, $prepare = true);
    
    /**
     * 插入多条记录
     * 
     * @param string $table
     * @param array $dataArray
     * @param boolean $prepare
     */
    abstract protected function insertAll($table, array $dataArray, $prepare = true);

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
     * @param array $params
     *            => [
     *            host          => (string) 主机（非必填，默认值为：localhost）
     *            port          => (string) 端口（非必填）
     *            dbname        => (string) 数据库名
     *            username      => (string) 用户名
     *            passwd        => (string) 密码
     *            charset       => (string) 字符集编码（非必填，默认值为：utf8）
     *            persistent    => (boolean) 是否启用持久连接（非必填，默认值为：false）
     *            ]
     * @return Db_Abstract
     */
    public function __construct($params)
    {
        if (! is_array($params)) {
            throw new Exception('Adapter params must be an array.');
        }
        $adapterClassName = get_class($this);
        if (! isset($params['host'])) {
            $params['host'] = 'localhost';
        }
        if (! isset($params['dbname'])) {
            throw new Exception('Database adapter (' . $adapterClassName . ') param [dbname] is not defined.');
        } elseif (! isset($params['username'])) {
            throw new Exception('Database adapter (' . $adapterClassName . ') param [username] is not defined.');
        } elseif (! isset($params['passwd'])) {
            throw new Exception('Database adapter (' . $adapterClassName . ') param [passwd] is not defined.');
        }
        if (! isset($params['charset'])) {
            $params['charset'] = 'utf8';
        }
        if (! isset($params['persistent'])) {
            $params['persistent'] = false;
        }
        $this->_params = $params;
    }

    /**
     * 事务开始（抽象）
     */
    abstract protected function _beginTransaction();

    /**
     * 事务开始
     */
    public function beginTransaction()
    {
        $this->_beginTransaction();
    }

    /**
     * 事务提交（抽象）
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
     */
    public function getLastSql()
    {
        return $this->_lastSql;
    }

    /**
     * 保存最后一次执行的SQL语句
     *
     * @param string $sql            
     */
    protected function _setLastSql($sql = null)
    {
        $this->_lastSql = $sql;
    }

    /**
     * 开启调试模式
     */
    public function debug()
    {
        $this->_debug = true;
        return $this;
    }

    /**
     * 输出调试信息
     * 
     * @param string $sql
     * @param array $values 
     */
    protected function _debugSql($sql, array $values = null)
    {
        echo "<p>----------DEBUG SQL BEGIN----------</p>" . PHP_EOL;
        echo "<p>SQL:</p>" . PHP_EOL;
        echo "<pre>$sql</pre>" . PHP_EOL;
        if ($values !== null) {
            echo "<p>VALUES:</p>" . PHP_EOL;
            dump($values);
        }
        echo "<p>----------DEBUG SQL END----------</p>" . PHP_EOL;
        die();
    }
}