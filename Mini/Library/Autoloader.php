<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Autoloader
{
    /**
     * Autoloader Instance
     * 
     * @var Autoloader
     */
    protected static $_instance;
    
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
     * 构造
     * 
     */
    protected function __construct()
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . MINI_PATH);
        spl_autoload_register(array(__CLASS__, 'Autoloader::autoload'));
    }
    
    /**
     * 自动载入
     * 
     * @param string $className
     */
    public function autoload($className)
    {
        $classPath = '';
        
        if (strpos($className, '_') !== false) {
            $classPath = strstr($className, '_', true) . DIRECTORY_SEPARATOR;
        }
        
        $file = MINI_PATH . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . $classPath . $className . '.php';
        
        if (file_exists($file)) {
            include_once($file);
        } else {
            throw new Exceptions('Library "' . $className . '" not found.');
        }
    }
}
