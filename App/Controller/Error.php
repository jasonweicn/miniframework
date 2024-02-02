<?php
namespace App\Controller;

use Mini\Base\Action;

/**
 * 这是一个自定义错误页
 */
class Error extends Action
{
    
    function indexAction($error)
    {
        $info = '';
        switch ($error['code']) {
            case 403:
                $info = '很抱歉，您没有访问这个资源的权限。';
                break;
            case 404:
                $info = '很抱歉，您访问的资源不存在。';
                break;
            default:
                $info = '很抱歉，我们的程序似乎出了些问题...';
        }
        $this->view->assign('title', '这是一个自定义的错误页');
        $this->view->assign('info', $info);
        $this->view->assign('error', $error);
        $this->view->display();
    }
    
}
