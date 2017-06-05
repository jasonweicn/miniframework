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
        $exceptions = Exceptions::getInstance();
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $className)) {
            if ($exceptions->throwExceptions()) {
                throw new Exception('Class name "' . $className . '"  invalid.');
            } else {
                $exceptions->sendHttpStatus(500);
            }
        }
        
        if (class_exists($className, false) || interface_exists($className, false)) {
            return;
        }
        
        $classFile = APP_PATH . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $className . '.php';
        
        if (file_exists($classFile)) {
            include_once($classFile);
        } else {
            if ($exceptions->throwExceptions()) {
                throw new Exception('Class file "' . $classFile . '" not found.');
            } else {
                $exceptions->sendHttpStatus(500);
            }
        }
        
        if (!class_exists($className, false) && !interface_exists($className, false)) {
            if ($exceptions->throwExceptions()) {
                throw new Exception('Class "' . $className . '" does not exist.');
            } else {
                $exceptions->sendHttpStatus(500);
            }
        }
    }
}
