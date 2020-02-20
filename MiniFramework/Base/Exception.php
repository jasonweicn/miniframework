<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2020 http://www.sunbloger.com
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
namespace Mini\Base;

class Exception extends \Exception
{

    /**
     * 构造
     *
     * @param string $message 错误信息
     * @param int $code 错误代码
     */
    public function __construct($message, $code = 0, $level = Log::ERROR, $position = null)
    {
        Log::record($message, $level, $position);
        parent::__construct($message, $code);
    }

    /**
     * 重构 toString
     */
    public function __toString()
    {
        if (SHOW_ERROR === true) {
            return parent::__toString();
        } else {
            self::showErrorPage($this->code);
        }
    }

    /**
     * 显示自定义的报错内容
     *
     * @param int $code
     */
    public static function showErrorPage($code)
    {
        $http = Http::getInstance();
        $status = $http->isStatus($code);

        if ($status === false) {
            $code = 500;
            $status = $http->isStatus($code);
        }

        $info = '<html><head><title>Error</title></head><body><h1>An error occurred</h1>';
        $info .= '<h2>' . $code . ' ' . $status . '</h2></body></html>';

        $http->header('Content-Type', 'text/html; charset=utf-8')->response($code, $info);

        die();
    }
}
