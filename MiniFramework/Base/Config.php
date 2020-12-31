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

class Config
{

    /**
     * Config Instance
     *
     * @var Config
     */
    protected static $_instance;

    private $_confData = [];

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
     * 读取配置
     *
     * @param string $config            
     */
    public function load($config)
    {
        $lastPos = strpos($config, ':');
        if ($lastPos !== false) {
            $confName = strstr($config, ':', true);
            $confKey = substr($config, $lastPos + 1);
        } else {
            $confName = $config;
        }
        
        if (! isset($this->_confData[$confName])) {
            
            $confFile = CONFIG_PATH . DS . $confName . '.php';
            
            if (file_exists($confFile)) {
                include ($confFile);
            } else {
                throw new Exception('Config "' . $confName . '" not found.');
            }
            
            if (isset(${$confName})) {
                $this->_confData[$confName] = ${$confName};
            } else {
                return null;
            }
        }
        
        if (isset($confKey) && isset($this->_confData[$confName][$confKey])) {
            return $this->_confData[$confName][$confKey];
        }
        
        return $this->_confData[$confName];
    }
}
