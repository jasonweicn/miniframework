<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://www.sunbloger.com
// +---------------------------------------------------------------------------
// | Licensed under the Apache License, Version 2.0 (the "License");
// | you may not use this file except in compliance with the License.
// | You may obtain a copy of the License at
// |
// | http://www.apache.org/licenses/LICENSE-2.0
// |
// | Unless required by applicable law or agreed to in writing, software
// | distributed under the License is distributed on an "AS IS" BASIS,
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// | See the License for the specific language governing permissions and
// | limitations under the License.
// +---------------------------------------------------------------------------
// | Source: https://github.com/jasonweicn/miniframework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------
namespace Mini\Console;

use Mini;

class Action
{
    /**
     * 构造
     */
    function __construct()
    {
        if (method_exists($this, '_init')) {
            $this->_init();
        }
    }

    /**
     * 析构
     */
    function __destruct()
    {
        if (method_exists($this, '_end')) {
            $this->_end();
        }
    }

    /**
     * 向命令行输出格式化的日志内容
     * 
     * @param string $message 自定义日志消息
     * @param string $level 自定义日志级别
     */
    function consoleLog($message, $level = 'INFO')
    {
        $level = strtoupper($level);
        if (! in_array($level, explode(',', LOG_LEVEL))) {
            $level = 'INFO';
        }
        $timestamp = microtime(true);
        $t = floor($timestamp);
        $m = sprintf("%03d", ($timestamp - floor($timestamp)) * 1000);
        $formattedTime = date('Y-m-d H:i:s', $t) . '.' . $m;

        printf("%s - [%s: %s]\n", $formattedTime, $level, $message);
    }
}
