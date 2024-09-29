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

class Config
{

    /**
     * Config Instance
     *
     * @var Config
     */
    protected static $_instance;

    private $_confData = [];
    
    private $_isThrowException = true;

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
     * 构造
     */
    final protected function __construct($isThrowException = true)
    {
        $this->_isThrowException = $isThrowException;
    }
    
    /**
     * 克隆
     */
    private function __clone()
    {}

    /**
     * 读取配置
     * 
     * @param string $config
     * @param boolean $throw
     * @throws Exception
     * @return boolean|NULL|mixed
     */
    public static function load($config, $throw = true)
    {
        $instance = self::getInstance();
        $instance->_isThrowException = $throw;
        $lastPos = strpos($config, ':');
        if ($lastPos !== false) {
            $confName = strstr($config, ':', true);
            $confKey = substr($config, $lastPos + 1);
        } else {
            $confName = $config;
        }
        
        // 如果未找到配置则尝试从配置文件读入
        if (!isset($instance->_confData[$confName])) {
            if (!$instance->setFromFile($confName)) {
                return false;
            }
        }
        
        if (isset($confKey)) {
            if (!isset($instance->_confData[$confName][$confKey])) {
                return $instance->report('Config "' . $confName . ':' . $confKey . '" not found.');
            }
            return $instance->_confData[$confName][$confKey];
        }
        
        return $instance->_confData[$confName];
    }
    
    /**
     * 设置配置项
     * 
     * @param mixed $value 配置的值
     * @param string $name 配置名
     * @return boolean
     */
    public static function set($value, string $name)
    {
        $instance = self::getInstance();
        if ($name === null) {
            return $instance->report('Invalid config name.');
        }
        list($name, $key) = $instance->parseConfigName($name);
        if ($key === null) {
            if (isset($instance->_confData[$name])) {
                return $instance->report('Config "' . $name . '" already exists.');
            }
            $instance->_confData[$name] = $value;
        } else {
            if (isset($instance->_confData[$name][$key])) {
                return $instance->report('Config "' . $name . ':' . $key . '" already exists.');
            }
            $instance->_confData[$name][$key] = $value;
        }
        
        return true;
    }
    
    /**
     * 从文件设置配置项
     * 
     * @param string $name 配置名
     * @return boolean|NULL
     */
    private function setFromFile($name)
    {
        $file = '';
        if (APP_ENV == 'prod') {
            $file = CONFIG_PATH . DS . $name . '.php';
        } else {
            $file = CONFIG_PATH . DS . $name . '-' . APP_ENV . '.php';
        }
        if (file_exists($file)) {
            $res = include($file);
        } else {
            return $this->report('Config "' . $name . '" not found.');
        }
        if ($res === 1) {
            if (isset(${$name})) {
                $this->_confData[$name] = ${$name};
            } else {
                return null;
            }
        } else {
            $this->_confData[$name] = $res;
        }
        
        return true;
    }
    
    /**
     * 解析配置名
     * 
     * @param string $name
     * @return array
     */
    private function parseConfigName(string $name)
    {
        $lastPos = strpos($name, ':');
        if ($lastPos !== false) {
            $confName = strstr($name, ':', true);
            $confKey = substr($name, $lastPos + 1);
        } else {
            $confName = $name;
            $confKey = null;
        }
        
        return [$confName, $confKey];
    }
    
    /**
     * 异常报告
     * 
     * @param string $msg
     * @return boolean
     */
    private function report(string $msg)
    {
        if ($this->_isThrowException === true) {
            throw new Exception($msg);
        }
        
        return false;
    }
}
