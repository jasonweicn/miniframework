<?php
namespace App\Controller;

use Mini\Base\{Action, Session, Upload, Log, Debug, Params, Response};
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
        if (! Session::has('example_session')) {
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
        $log_status = LOG_ON === true ? '开启' : '关闭';
        $log_mode = '';
        if (LOG_MODE == 1) {
            $log_mode = '文件';
        } elseif (LOG_MODE == 2) {
            $log_mode = '数据库';
        } else {
            $log_mode = '未知';
        }
        $this->view->assign('message', $message);
        $this->view->assign('log_status', $log_status);
        $this->view->assign('log_mode', $log_mode);
        // 如果 LOG_ON 为 true 时，下面的内容会在程序运行结束时最终写入日志文件
        Log::record($message, 'INFO', ['file'=>__FILE__, 'line'=>__LINE__]);
        $this->view->display();
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
        
        // 指定用 sha1 来进行加密（默认为：md5）
        $signObj->setEncryptType('sha1');
        
        // 获得一个签名
        $sign = $signObj->sign($data);
        
        // 签名随其他数据一起通过GET传递
        $data['sign'] = $sign;
        dump($data);
        
        // 构造一个GET请求URL
        $dataStr = arrayToUrlParams($data);
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
        
        // 验证时需要使用相同的加密方式
        $signObj->setEncryptType('sha1');
        
        // 设定签名过期时间为30秒（默认为：10秒）
        $signObj->setExpireTime(30);
        
        // 获得签名校验结果，传入参数get代表对GET请求进行签名校验
        $res = $signObj->verifySign('get');
        dump($res);
        
        die();
    }
    
    /**
     * Example 8: Route
     */
    function routeAction()
    {
        //自定义的路由规则配置在 Config/route.php 中
        
        $id = Params::getInstance()->getParam('id');
        if ($id === null) {
            $id = 'NULL';
        }
        $this->view->assign('id', $id);
        $this->view->display();
    }

    /**
     * Example 9: Response
     */
    function responseAction()
    {
        // 输出的内容
        $data = 'Hello MiniFramework!';
        
        // 获取 Response 实例
        $response = Response::getInstance();
        
        // 设定响应状态码和类型，并通过 send 方法将内容发送给客户端。
        $response->httpStatus(200)->type('json')->send(json_encode($data));
    }
}
