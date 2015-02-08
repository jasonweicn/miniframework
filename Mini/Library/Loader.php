<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Loader
{
    /**
     * 载入Class
     * 
     * @param string $className
     */
    public static function loadClass($className)
    {
        if (class_exists($className, false) || interface_exists($className, false)) {
            return;
        }
        
        $classFile = APP_PATH . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $className . '.php';
        
        if (file_exists($classFile)) {
            include_once($classFile);
        } else {
            throw new Exception('Class file "' . $classFile . '" not found.');
        }
        
        if (!class_exists($className, false) && !interface_exists($className, false)) {
            throw new Exception('Class "' . $className . '" does not exist.');
        }
    }
}