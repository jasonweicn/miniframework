<?php
namespace App\Controller;

use Mini\Action;

/**
 * 这是一个控制器的案例
 */
class Request extends Action
{
    /**
     * 默认动作
     */
    function indexAction()
    {
        $headers['testA'] = 'a';
        $headers['test-B'] = 2;
        
        $headerArr = array();
        foreach( $headers as $n => $v ) {
            $headerArr[] = $n .':' . $v;
        }
        
        ob_start();
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, "http://localhost/App/Public/");
        curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr );  //构造IP
        curl_setopt ($ch, CURLOPT_REFERER, "http://www.163.com/ ");   //构造来路
        curl_setopt( $ch, CURLOPT_HEADER, 1);
        curl_exec($ch);
        curl_close ($ch);
        $out = ob_get_contents();
        ob_clean();
        echo $out;
        die();
    }
}
