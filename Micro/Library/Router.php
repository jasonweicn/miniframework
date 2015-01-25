<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Router
{
    protected $_controller;
    public $_action;
    
    public function __construct()
    {
        $this->setController();
        $this->setAction();
    }
    
    /**
     * 路由
     * 
     */
    public function route()
    {
        $class = $this->getController();
        $target = APP_PATH . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $class . '.php';
        
        //引入目标文件
        if (file_exists($target)) {
            include_once($target);
            
            //Example: NewsController
            $className = ucfirst($class) . 'Controller';
            if (class_exists($className)) {
                $controller = new $className($class, $this->_action);
            } else {
                throw new Exception($className . ' does not exist.');
            }
        } else {
            throw new Exception('Controller ' . $class . ' not found.');
        }
        return $controller;
    }
    
    public function setController()
    {
        if ($this->_controller === null) {
            $this->_controller = isset($_GET['c']) ? $_GET['c'] : 'index';
        }
        return $this->_controller;
    }
    
    public function getController()
    {
        return $this->_controller;
    }
    
    public function setAction()
    {
        if ($this->_action === null) {
            $this->_action = isset($_GET['a']) ? $_GET['a'] : 'index';
        }
        return $this->_action;
    }
    
    public function getAction()
    {
        return $this->_action;
    }
}