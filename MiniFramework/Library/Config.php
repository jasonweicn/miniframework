<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

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
