<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

namespace Mini;

class Loader
{
    /**
     * Loader Instance
     * 
     * @var Loader
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
        spl_autoload_register(array(__CLASS__, 'Mini\Loader::autoload'));
    }
    
    /**
     * 自动载入
     * 
     * @param string $class
     */
    public function autoload($class)
    {
        try {
            $this->loadClass($class);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * 载入类
     * @param string $class
     * @throws Exceptions
     */
    public static function loadClass($class)
    {
        $className = ltrim($class, '\\');
        $classPath = '';
        $namespace = '';
        $lastPos = strripos($className, '\\');
        
        if ($lastPos !== false) {
            $namespace = str_replace('Mini', 'Library', substr($className, 0, $lastPos));
            $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
            $name = strstr($namespace, '\\', true);
            
            if ($name == APP_NAMESPACE) {
                $classPath = APP_PATH . ltrim($classPath, APP_NAMESPACE);
            } else {
                $classPath = MINI_PATH . DIRECTORY_SEPARATOR . $classPath;
            }
            
            $className = substr($className, $lastPos + 1);
        } else {
            $classPath = APP_PATH . DIRECTORY_SEPARATOR . 'Model';
        }
        
        $classfile = $classPath . DIRECTORY_SEPARATOR . $className . '.php';
        
        if (file_exists($classfile)) {
            include_once($classfile);
        } else {
            throw new Exceptions('Library "' . $className . '" not found.');
        }
        
        return true;
    }
}
