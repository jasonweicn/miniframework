<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2018 http://www.sunbloger.com
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
// | Source: https://github.com/jasonweicn/MiniFramework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------
namespace Mini;

abstract class Model
{

    /**
     * 数据库对象池
     *
     * @var object
     */
    protected $_dbPool;

    /**
     * 构造
     *
     * @return Action
     */
    function __construct()
    {
        if (DB_AUTO_CONNECT === true) {
            $this->_dbPool = App::getInstance()->getDbPool();
        }
    }

    /**
     * 加载数据库对象
     *
     * @param string $key
     * @return NULL|object
     */
    public function loadDb($key)
    {
        if (! isset($this->_dbPool[$key])) {
            return null;
        }
        
        return $this->_dbPool[$key];
    }
}
