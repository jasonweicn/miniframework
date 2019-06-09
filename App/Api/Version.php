<?php
namespace App\Api;

use Mini\Base\Rest;

/**
 * 这是一个应用REST模式的API接口案例
 */
class Version extends Rest
{

    /**
     * 初始化
     */
    function _init()
    {
        // do something...
    }

    /**
     * GET
     */
    function get()
    {
        $version = '1.0.0';
        
        $type = $this->params->getParam('type');
        
        if (empty($type) || $type == 'json') {
            
            // 输出JSON
            $this->responseJson(200, 'success', $version);
        } elseif ($type == 'xml') {
            
            // 输出XML
            $this->responseXml(200, 'success', array(
                'version' => $version
            ));
        }
    }

    /**
     * POST
     */
    function post()
    {
        // 获取POST参数
        $params = $this->params->getParams();
        
        $this->responseJson(201, 'success', $params);
    }

    /**
     * PUT
     */
    function put()
    {
        // 禁止访问输出403（ forbidden()方法从 1.0.6 版开始支持 ）
        $this->forbidden();
    }

    /**
     * DELETE
     */
    //function delete()
    //{
        // 当某个方法不存在时，会自动输出403
    //}
}
