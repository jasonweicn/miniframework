<?php
class NewsController extends Action
{
    function init()
    {
        $this->view->title = 'Micro Framework';
    }
    
    function mainAction()
    {
        Loader::loadClass('news');
        $news = new News();
        $info = $news->getInfo();
        $this->view->assign('info', $info);
    }
}
?>