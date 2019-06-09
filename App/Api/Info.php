<?php
namespace App\Api;

use Mini\Base\Rest;

/**
 * 这是一个演示API版本控制的demo
 */
class Info extends Rest
{

    /**
     * GET
     */
    function get()
    {
        $info = 'Hello World!';
        
        // 输出JSON
        $this->responseJson(200, 'success', $info);
    }
}
