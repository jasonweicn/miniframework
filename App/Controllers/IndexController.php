<?php
class IndexController extends Action
{
    function _init()
    {
        $this->view->title = 'Mini Framework';
    }
    
    function indexAction()
    {
        $this->view->assign('info', 'Hello World!');
        $this->view->display();
    }
}