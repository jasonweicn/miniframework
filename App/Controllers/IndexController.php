<?php
class IndexController extends Action
{
    function _init()
    {
        $this->view->title = 'MiniFramework';
        
        //使用布局，需在入口文件 Public/index.php 中定义常量 LAYOUT_ON 的值为 true
        $this->view->_layout->setLayout('default');
        $this->view->_layout->header = $this->view->render(LAYOUT_PATH . '/header.php');
    }
    
    function indexAction()
    {
        Loader::loadClass('Info');
        $info = new Info();
        $info_text = $info->getInfo();
        
        $this->view->assign('info', $info_text);
        $this->view->display();
    }
}
