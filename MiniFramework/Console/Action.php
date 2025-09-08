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
     * Params实例
     *
     * @var Params
     */
    protected $params;

    /**
     * Request实例（旧名称）
     *
     * @var Mini\Base\Request
     */
    protected $_request;

    /**
     * Request实例（新名称）
     * 
     * @var Mini\Base\Request
     */
    protected $request;

    /**
     * Response实例
     *  
     * @var Mini\Base\Response
     */
    protected $response;

    /**
     * 构造
     *
     * @param string $controller            
     * @param string $action            
     * @return Action
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
    function __destruct() {
        if (method_exists($this, '_end')) {
            $this->_end();
        }
    }
}
