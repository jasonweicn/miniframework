<?php
namespace App\Controller;

use Mini\Base\Action;

/**
 * 这是一个控制器的案例
 */
class Index extends Action
{
    /**
     * 初始化
     */
    function _init()
    {
        $this->view->title = 'MiniFramework';
        
        // 使用布局，需在入口文件 Public/index.php 中定义常量 LAYOUT_ON 的值为 true
        $this->view->_layout->setLayout('default');
        $this->view->_layout->header = $this->view->render(LAYOUT_PATH . '/header.php');
    }
    
    /**
     * 默认动作
     */
    function indexAction()
    {
        // 实例化一个模型
        $info = new \App\Model\Info();
        
        // 调用模型中的方法
        $infoText = $info->getInfo();
        
        // 向View传值
        $this->view->assign('info', $infoText);
        
        // 渲染并显示View
        $this->view->display();
    }
}
