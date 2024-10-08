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

abstract class Model extends Query
{
    
    /**
     * 数据表名
     * 
     * @var string
     */
    private $_table = '';
    
    /**
     * 主键名称（默认值：id）
     * 
     * @var string
     */
    private $_primaryKey = 'id';
    
    /**
     * 原始主键值
     * 
     * @var mixed
     */
    private $_originalPrimaryKeyValue;
    
    /**
     * 原始属性
     * 
     * @var array
     */
    private $_originalProperty = [];

    /**
     * 构造方法
     * 
     * @return void
     */
    function __construct(object $db = null)
    {
        if (DB_AUTO_CONNECT === true && $db === null) {
            $db = $this->loadDb('default');
            
        }
        if ($db) {
            parent::__construct($db);
        }
        $this->_table = $this->tableName();
        $this->initProperty();
    }

    /**
     * 加载数据库对象
     *
     * @param string $key
     * @return NULL|object
     */
    public function loadDb(string $key)
    {
        return App::loadDb($key);
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
        return App::regDb($key, $params);
    }
    
    /**
     * 获取数据表名
     * 
     * @return string
     */
    protected function tableName()
    {
        if ($this->_table === null) {
            throw new Exception('Undefined table name.');
        }
        
        return $this->_table;
    }
    
    /**
     * 获取主键名
     * 
     * @return string
     */
    protected function primaryKeyName()
    {
        if ($this->_primaryKey === null) {
            throw new Exception('Undefined primary key name.');
        }
        
        return $this->_primaryKey;
    }
    
    /**
     * 按主键查找记录
     *
     * @param mixed $id 主键值
     * @return $this
     */
    public function findById($id)
    {
        return $this->findByField($id, $this->primaryKeyName());
    }
    
    /**
     * 按指定字段查找记录
     *
     * @param mixed $value 字段值
     * @param string $fieldName 字段名称
     * @return $this | false
     */
    public function findByField($value, string $fieldName)
    {
        $this->table($this->tableName());
        $this->where($this->primaryKeyName(), '=', $value);
        $row = $this->select('row');
        $primaryKeyValue = null;
        if ($row) {
            $this->setProperty($row);
            $primaryKeyValue = $row[$this->primaryKeyName()];
            $this->setOriginalPrimaryKeyValue($primaryKeyValue);
        } else {
            return false;
        }
        
        return $this;
    }
    
    /**
     * 按多个字段条件查找记录
     *
     * @param array $conditions 条件数组，例如：[k1=>v1,k2=>v2]
     * @param string $logicSymbol 逻辑符号，例如： AND 或 OR
     * @return $this | false
     */
    public function findByFields(array $conditions, $logicSymbol = 'AND')
    {
        $this->table($this->tableName());
        $this->where($conditions, strtoupper($logicSymbol));
        $row = $this->select('row');
        $primaryKeyValue = null;
        if ($row) {
            $this->setProperty($row);
            $primaryKeyValue = $row[$this->primaryKeyName()];
            $this->setOriginalPrimaryKeyValue($primaryKeyValue);
        } else {
            return false;
        }
        
        return $this;
    }

    /**
     * 查询多条记录。
     *
     * @param array $conditions 条件数组
     * @return array 模型数组
     */
    public function findAll(array $conditions = [], $logicSymbol = 'AND')
    {
        $this->table($this->tableName());
        if (!empty($conditions)) {
            $this->where($conditions, $logicSymbol);
        }
        $rows = $this->select('all');
        $objects = [];
        foreach ($rows as $row) {
            $instance = clone $this;
            $instance->setProperty($row);
            $instance->setOriginalPrimaryKeyValue($row[$instance->primaryKeyName()]);
            $objects[] = $instance;
        }
        
        return $objects;
    }

    /**
     * 从数据库中删除当前记录
     * 
     * @return int
     */
    public function remove()
    {
        if ($this->_originalPrimaryKeyValue === null) {
            throw new Exception("Primary key value is not set.");
        }
        $this->table($this->tableName());
        $this->where($this->primaryKeyName(), '=', $this->_originalPrimaryKeyValue);
        $res = $this->delete();
        
        return $res;
    }

    /**
     * 持久化存储（将当前模型中改变的属性保存至数据库）
     *
     * @return bool 是否执行成功
     */
    public function persist()
    {
        // 检查属性值的变化
        $persistData = [];
        foreach ($this->_originalProperty as $name => $value) {
            if (property_exists($this, $name) && $this->$name != $value) {
                $persistData[$name] = $this->$name;
            }
        }
        if (empty($persistData)) {
            return true;
        }
        
        // 保存数据（主键为 null 时返回 false）
        $this->table($this->tableName());
        $this->data($persistData);
        if ($this->_originalPrimaryKeyValue === null) {
            return false;
        } else {
            $this->where($this->primaryKeyName(), $this->_originalPrimaryKeyValue);
            $res = $this->save();
        }
        
        // 如果变更项涉及主键，则更新原始主键值属性
        if (isset($persistData[$this->primaryKeyName()])) {
            $this->setOriginalPrimaryKeyValue($persistData[$this->primaryKeyName()]);
        }
        
        return $res;
    }
    
    /**
     * 设置原主键值
     * 
     * @param mixed $value
     * @return void
     */
    private function setOriginalPrimaryKeyValue($value)
    {
        if (is_array($value)) {
            throw new Exception('The primary key value cannot be an array..');
        }
        $this->_originalPrimaryKeyValue = $value;
    }
    
    /**
     * 创建一个包含指定数据作为属性的对象。
     *
     * @param array $data 关联数组
     * @return object
     */
    private function createObject(array $data)
    {
        $instance = new \stdClass();
        foreach ($data as $key => $value) {
            $instance->$key = $value;
        }
        
        return $instance;
    }
    
    /**
     * 初始化子类中定义的字段属性
     * 
     * @return void
     */
    private function initProperty()
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        $currentClass = $reflection;
        foreach ($properties as $property) {
            if ($property->isPrivate()) {
                continue;
            }
            $declaringClass = $property->getDeclaringClass();
            if ($declaringClass == $currentClass) {
                $propertyName = $property->getName();
                $this->$propertyName = null;
                $this->_originalProperty[$propertyName] = null;
            }
        }
    }
    
    /**
     * 填充对象属性
     * 
     * @param array $row
     * @return void
     */
    protected function setProperty($row)
    {
        foreach ($row as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->_originalProperty[$key] = $value;
            }
        }
    }

    /**
     * 打印对象可见属性（用于调试代码）
     * 
     * @param string $type 打印方式 ( object | array)
     * @return void
     */
    public function dump(string $type = 'object')
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        $propertys = [];
        $currentClass = $reflection;
        foreach ($properties as $property) {
            if ($property->isPrivate()) {
                continue;
            }
            $declaringClass = $property->getDeclaringClass();
            if ($declaringClass == $currentClass) {
                $propertyName = $property->getName();
                $propertyValue = $property->getValue($this);
                if (is_array($propertyValue)) {
                    $propertys[$propertyName] = json_encode($propertyValue);
                } elseif (is_object($propertyValue)) {
                    $propertys[$propertyName] = get_class($propertyValue);
                } else {
                    $propertys[$propertyName] = $propertyValue;
                }
            }
        }
        if ($type == 'object' || ! in_array($type, ['object', 'array'])) {
            dump($this->createObject($propertys));
        } elseif ($type == 'array') {
            dump($propertys);
        }
    }
}
