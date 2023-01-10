<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2023 http://www.sunbloger.com
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
     * 构造
     */
    final protected function __construct()
    {}
    
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
    public function load($config, $throw = true)
    {
        $lastPos = strpos($config, ':');
        if ($lastPos !== false) {
            $confName = strstr($config, ':', true);
            $confKey = substr($config, $lastPos + 1);
        } else {
            $confName = $config;
        }
        if (! isset($this->_confData[$confName])) {
            if (APP_ENV == 'prod') {
                $confFile = CONFIG_PATH . DS . $confName . '.php';
            } else {
                $confFile = CONFIG_PATH . DS . $confName . '-' . APP_ENV . '.php';
            }
            if (file_exists($confFile)) {
                $res = include($confFile);
            } else {
                if ($throw === true) {
                    throw new Exception('Config "' . $confName . '" not found.');
                } else {
                    return false;
                }
            }
            if ($res === 1) {
                if (isset(${$confName})) {
                    $this->_confData[$confName] = ${$confName};
                } else {
                    return null;
                }
            } else {
                $this->_confData[$confName] = $res;
            }
        }
        if (isset($confKey) && isset($this->_confData[$confName][$confKey])) {
            return $this->_confData[$confName][$confKey];
        }
        
        return $this->_confData[$confName];
    }
}
