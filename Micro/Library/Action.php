<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Action
{
    public $view;
    
    function __construct($controller, $action)
    {
        $this->view = new View($controller, $action);
    }
    
    public function assign($variable, $value)
    {
        $this->view->assign($variable, $value);
    }
    
    public function init() {}
}