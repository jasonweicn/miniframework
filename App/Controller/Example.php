<?php
namespace App\Controller;

use Mini\Base\{Action, Session, Upload, Log, Debug};
use Mini\Captcha\Captcha;

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
        Log::record($message, 'INFO', ['file'=>__FILE__, 'line'=>__LINE__]);
        
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
    
    /**
     * Example 6: Sign
     */
    function signAction()
    {
        // 待签名的数据
        $data = [
            'info' => 'MiniFramework',
            
            // signTime为当前时间戳，且必须随数据一起进行签名
            'signTime' => time()
        ];
        
        $signObj = new \Mini\Security\Sign();
        
        // 获得一个签名
        $sign = $signObj->sign($data);
        
        // 签名随其他数据一起通过GET传递
        $data['sign'] = $sign;
        dump($data);
        
        // 构造一个GET请求URL
        $tmp = [];
        foreach ($data as $key => $val) {
            $tmp[] = $key . '=' . $val;
        }
        $dataStr = implode('&', $tmp);
        $url = $this->view->baseUrl() . '/example/verifysign?' . $dataStr;
        echo '<a href="' . $url . '" target="_blank">Click to verify sign</a>';
        
        die();
    }
    
    /**
     * Example 7: Verify Sign
     */
    function verifysignAction()
    {
        $signObj = new \Mini\Security\Sign();
        
        // 设定签名过期时间为30秒（默认为：300秒）
        $signObj->setExpireTime(30);
        
        // 获得签名校验结果，传入参数get代表对GET请求进行签名校验
        $res = $signObj->verifySign('get');
        dump($res);
        
        die();
    }
}
