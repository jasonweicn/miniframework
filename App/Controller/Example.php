<?php
namespace App\Controller;

use Mini\Base\Action;
use Mini\Captcha\Captcha;
use Mini\Base\Session;
use Mini\Base\Upload;
use Mini\Base\Log;
use Mini\Base\Debug;

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
            $upload = new Upload();
            $res = $upload->save($_FILES); // or $_FILES['f1']
            
            echo "<br />ErrorMsg:";
            $errmsg = $upload->getErrorMsg();
            dump($errmsg);
            
            echo "<br />Result:";
            dump($res);
        }
        
        $this->view->display();
    }
    
    /**
     * Example 4: Log
     */
    function logAction()
    {
        $message = 'This is a log test.';

        // 如果 LOG_ON 为 true 时，下面的内容会在程序运行结束时最终写入日志文件
        Log::record($message, 'INFO', array('file'=>__FILE__, 'line'=>__LINE__));
        
    }
    
    /**
     * Example 5: Debug(timer)
     */
    function debugtimerAction()
    {
        // 计时开始
        Debug::timerStart();
        
        sleep(1);
        
        // 纪录中间计时点
        Debug::timerPoint();
        
        sleep(1);
        
        // 再次纪录中间计时点
        Debug::timerPoint();
        
        sleep(1);
        
        // 计时结束
        Debug::timerEnd();
        
        // 直接 dump 计时结果的数组
        Debug::getTimerRecords(true);
        
        die();
    }
}
