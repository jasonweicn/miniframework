<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

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
     * @param string $configName
     */
    public function load($configName)
    {
        if (!isset($this->_configArray[$configName])) {
            
            $configFile = CONFIG_PATH . DIRECTORY_SEPARATOR . $configName . '.php';
        
            if (file_exists($configFile)) {
                include($configFile);
            } else {
                throw new Exceptions('Config "' . $configName . '" not found.');
            }
            
            if (isset(${$configName})) {
                $config = ${$configName};
                $this->_configArray[$configName] = $config;
            } else {
                return null;
            }
        } else {
            $config = $this->_configArray[$configName];
        }
        
        return $config;
    }
}
