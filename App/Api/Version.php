<?php
namespace App\Api;

use Mini\Rest;

//这是一个应用REST模式的API接口案例
class Version extends Rest
{
    function _init()
    {
        //do something...
    }
    
    function get()
    {
        $version = '1.0.0';
        
        $type = $this->params->getParam('type');
        
        if (empty($type) || $type == 'json') {
            
            //输出JSON
            $this->responseJson(200, 'success', $version);
            
        } elseif ($type == 'xml') {
            
            //输出XML
            $this->responseXml(200, 'success', array('version' => $version));
            
        }
    }
    
    function post()
    {
        //获取POST参数
        $params = $this->params->getParams();
        
        //do something...
    }
    
    function put()
    {
        //获取PUT参数（POST和PUT参数均可通过此方法获得）
        $params = $this->params->getParams();
        
        //do something...
    }
    
    function delete()
    {
        //返回HTTP状态码403（在REST中表示对于拒绝访问）
        $this->responseJson(403);
    }
}
