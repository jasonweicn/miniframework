<?php
namespace App\Api;

use Mini\Base\Rest;

/**
 * 这是一个演示API版本控制的demo
 * 说明：向Header中加入Ver（版本号）参数，Ver的值必须是一个整数；API文件名和类名加后缀_VX（X为版本号）
 */
class Info_V2 extends Rest
{

    /**
     * GET
     */
    function get()
    {
        // 读取Header数据
        $headers = $this->_request->getHeaders();
        
        $info = 'Hello World!(V' . $headers['Ver'] . ')';
        
        // 输出（默认为 JSON 格式）
        $this->response(200, 'success', $info);
    }
}
