<?php
// +--------------------------------------------------------------------------------
// | Mini Framework
// +--------------------------------------------------------------------------------
// | Copyright (c) 2015-2017 http://www.sunbloger.com
// +--------------------------------------------------------------------------------
// | Licensed under the Apache License, Version 2.0 (the "License");
// | you may not use this file except in compliance with the License.
// | You may obtain a copy of the License at
// |
// |   http://www.apache.org/licenses/LICENSE-2.0
// |
// | Unless required by applicable law or agreed to in writing, software
// | distributed under the License is distributed on an "AS IS" BASIS,
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// | See the License for the specific language governing permissions and
// | limitations under the License.
// +--------------------------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +--------------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +--------------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +--------------------------------------------------------------------------------

namespace Mini;

class Config
{
    /**
     * Config Instance
     * 
     * @var Config
     */
    protected static $_instance;
    
    private $_configArray = array();
    
    /**
     * 获取实例
     * 
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 读取配置
     * 
     * @param string $config
     */
    public function load($config)
    {
        $lastPos = strpos($config, ':');
        if ($lastPos !== false) {
            $configName = strstr($config, ':', true);
            $configKey = substr($config, $lastPos+1);
        } else {
            $configName = $config;
        }
        
        if (!isset($this->_configArray[$configName])) {
            
            $configFile = CONFIG_PATH . DIRECTORY_SEPARATOR . $configName . '.php';
        
            if (file_exists($configFile)) {
                include($configFile);
            } else {
                throw new Exceptions('Config "' . $configName . '" not found.');
            }
            
            if (isset(${$configName})) {
                $configData = ${$configName};
                $this->_configArray[$configName] = $configData;
            } else {
                return null;
            }
        } else {
            $configData = $this->_configArray[$configName];
        }
        
        if (isset($configKey) && isset($configData[$configKey])) {
            return $configData[$configKey];
        }
        
        return $configData;
    }
}
