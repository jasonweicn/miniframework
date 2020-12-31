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

class Debug
{

    /**
     * 计时器
     * 
     * @var array
     */
    public static $timer;
    
    /**
     * 计时开始
     */
    public static function timerStart()
    {
        self::$timer = [];
        self::$timer['start'] = microtime(true);
    }
    
    /**
     * 计时点
     */
    public static function timerPoint()
    {
        self::$timer['point'][] = microtime(true);
    }
    
    /**
     * 计时结束
     */
    public static function timerEnd()
    {
        self::$timer['end'] = microtime(true);
    }
    
    /**
     * 获取计时信息
     * 
     * @param boolean $dump
     * @return boolean|string|array
     */
    public static function getTimerRecords($dump = true)
    {
        if (! isset(self::$timer['start']) || ! isset(self::$timer['end'])) {
            return false;
        }
        
        $records = array();
        
        $records['time'] = number_format((self::$timer['end'] - self::$timer['start']) * 1000, 4) . 'ms';
        if (isset(self::$timer['point'])) {
            foreach (self::$timer['point'] as $pt) {
                $records['point'][] = number_format(($pt - self::$timer['start']) * 1000, 4) . 'ms';
            }
        }
        
        if ($dump === true) {
            dump($records);
        }
        
        return $records;
    }
    
    /**
     * 判断变量类型
     * 
     * @param mixed $var
     * @return string
     */
    public static function varType($var)
    {
        if (is_object($var)) return "object";
        if (is_resource($var)) return "resource";
        if (is_array($var)) return "array";
        if (is_bool($var)) return "boolean";
        if (is_int($var)) return "integer";
        if (is_float($var)) return "float";
        if (is_null($var)) return "NULL";
        if (is_numeric($var)) return "numeric";
        if (is_string($var)) return "string";
        
        return "unknown";
    }
}
