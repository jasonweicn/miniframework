<?php
namespace App\Controller;

use Mini\Action;
use Mini\Captcha;
use Mini\Session;

/**
 * Example
 */
class Example extends Action
{
    /**
     * Example 1: Captcha
     */
    function captchaAction()
    {
        if (!empty($_POST['code'])) {
            $captcha = new Captcha();
            $res = $captcha->check($_POST['code']);
            if ($res) {
                $this->view->assign('info', 'success');
            } else {
                $this->view->assign('info', 'fail');
            }
            $this->view->assign('code', $_POST['code']);
        }
        
        $this->view->display();
    }
    
    function getcaptchaAction()
    {
        $captcha = new Captcha();
        $captcha->create();
    }
    
    /**
     * Example 2: Session
     */
    function sessionAction()
    {
        $t = time();
        $this->view->assign('t', $t);
        
        Session::start();
        if (! Session::is_set('example_session')) {
            Session::set('example_session', $t);
        }
        
        $this->view->assign('session_time', Session::get('example_session'));
        
        $this->view->display();
    }
    
    /**
     * Example 3: Upload
     */
    function uploadAction()
    {
        if (! empty($_FILES)) {
            $upload = new \Mini\Upload();
            $res = $upload->save($_FILES); // or $_FILES['f1']
            
            echo "<br />ErrorMsg:";
            $errmsg = $upload->getErrorMsg();
            dump($errmsg);
            
            echo "<br />Result:";
            dump($res);
        }
        
        $this->view->display();
    }
}
