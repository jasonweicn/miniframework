<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class View
{
    private $_render;

    function __construct($controller, $action)
    {
        $file = APP_PATH . DIRECTORY_SEPARATOR .  'Views' . DIRECTORY_SEPARATOR . strtolower($controller) . DIRECTORY_SEPARATOR . $action . '.php';
        
        if (file_exists($file)) {
            $this->_render = $file;
        } else {
            throw new Exception('View ' . $action . ' does not exist.');
        }
    }
    
    public function __set($variable, $value)
    {
        $this->assign($variable, $value);
    }

    /**
     * 接收来自于控制器的变量
     * 
     * @param string $variable
     * @param mixed $value
     */
    public function assign($variable, $value)
    {
        if (substr($variable, 0, 1) != '_') {
            $this->$variable = $value;
            return true;
        }
        return false;
    }

    public function __destruct()
    {
        //渲染视图
        include($this->_render);
    }  
}