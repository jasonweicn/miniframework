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
        $file = APP_PATH . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $className . '.php';
        if (file_exists($file)) {
            include_once($file);
        } else {
            throw new Exception('Class file "' . $filename . '" not found.');
        }
    }
}