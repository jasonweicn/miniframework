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
namespace Mini\Base;

class Registry extends \ArrayObject
{

    /**
     * Registry Instance
     *
     * @var Registry
     */
    protected static $_instance;

    /**
     * 获取实例
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 存入
     *
     * @param string $index            
     * @param mixed $value            
     */
    public static function set($index, $value)
    {
        $instance = self::getInstance();
        $instance->offsetSet($index, $value);
    }

    /**
     * 读出
     *
     * @param string $index            
     * @return mixed
     */
    public static function get($index)
    {
        $instance = self::getInstance();
        
        if (! $instance->offsetExists($index)) {
            throw new Exception('"' . $index . '" not registered.');
        }
        
        return $instance->offsetGet($index);
    }
    
    /**
     * 删除
     * @param string $index
     * @throws Exception
     * @return boolean
     */
    public static function del($index)
    {
        $instance = self::getInstance();
        
        if (! $instance->offsetExists($index)) {
            throw new Exception('"' . $index . '" not registered.');
        }
        
        $instance->offsetUnset($index);
        
        return true;
    }
}
