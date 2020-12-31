<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2021 http://www.sunbloger.com
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
namespace Mini\Cache;

use Mini\Base\Exception;

class Redis extends Cache_Abstract
{

    /**
     * 连接
     */
    protected function _connect()
    {
        if ($this->_cache_server)
            return;
        
        try {
            $this->_cache_server = new \Redis();
            $this->_cache_server->connect($this->_params['host'], $this->_params['port']);
        } catch (Exception $e) {
            throw new Exception($e);
        }
        
        if (isset($this->_params['passwd'])) {
            $authStatus = $this->_cache_server->auth($this->_params['passwd']);
            if ($authStatus === false) {
                throw new Exception('Redis connection failed.');
            }
        }
    }

    public function set($name, $value, $expire = null)
    {
        $this->_connect();
        if (! isset($expire) || empty($expire)) {
            $result = $this->_cache_server->set($name, $value);
        } else {
            $result = $this->_cache_server->setex($name, $expire, $value);
        }
        
        return $result;
    }

    public function get($name)
    {
        $this->_connect();
        return $this->_cache_server->get($name);
    }

    public function del($name)
    {
        $this->_connect();
        return $this->_cache_server->del($name);
    }

    /**
     * 获取Redis实例化对象，便于使用其他未封装的方法
     *
     * @return object
     */
    public function getRedisObj()
    {
        $this->_connect();
        return $this->_cache_server;
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        try {
            $this->_cache_server->close();
            $this->_cache_server = null;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
