<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2017 http://www.sunbloger.com
// +---------------------------------------------------------------------------
// | Licensed under the Apache License, Version 2.0 (the "License");
// | you may not use this file except in compliance with the License.
// | You may obtain a copy of the License at
// |
// |   http://www.apache.org/licenses/LICENSE-2.0
// |
// | Unless required by applicable law or agreed to in writing, software
// | distributed under the License is distributed on an "AS IS" BASIS,
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// | See the License for the specific language governing permissions and
// | limitations under the License.
// +---------------------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------

namespace Mini;

class Exceptions extends \Exception
{
    /**
     * 构造
     *
     */
    public function __construct($message, $code = 0)
    {
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
    private function showErrorPage($code)
    {
        $status = Http::isStatus($code);
        
        if ($status === false) {
            $code = 500;
        }
        
        $flag = Http::sendHttpStatus($code);
        
        if ($flag === true) {
            $errMsg = $status;
        } else {
            $errMsg = 'unknown error';
        }
        
        $info = '<html><head><title>Error</title></head><body><h1>An error occurred</h1>';
        $info.= '<h2>' . $code . ' ' . $errMsg . '</h2></body></html>';
        echo $info;
        
        die();
    }
}
