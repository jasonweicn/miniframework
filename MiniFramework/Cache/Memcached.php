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

class Memcached extends Cache_Abstract
{

    /**
     * 连接
     */
    protected function _connect()
    {
        if ($this->_cache_server)
            return;
        
        try {
            $this->_cache_server = new \Memcached();
            $this->_cache_server->addServer($this->_params['host'], $this->_params['port']);
        } catch (Exception $e) {
            throw new Exception($e);
        }
        
        $memStats = $this->_cache_server->getStats();
        $available = (bool) $memStats[$this->_params['host'] . ':' . $this->_params['port']];
        if (! $available) {
            throw new Exception('Memcache connection failed.');
        }
    }

    public function set($name, $value, $expire = null)
    {
        if (! isset($expire) || empty($expire)) {
            $expire = 0;
        }
        $this->_connect();
        if ($this->_compress_flag === true) {
            $this->_cache_server->setOption(\Memcached::OPT_COMPRESSION, true);
        }
        
        return $this->_cache_server->set($name, $value, $expire);
    }

    public function get($name)
    {
        $this->_connect();
        return $this->_cache_server->get($name);
    }

    public function del($name)
    {
        $this->_connect();
        return $this->_cache_server->delete($name);
    }

    /**
     * 获取Memcached实例化对象，便于使用其他未封装的方法
     *
     * @return object
     */
    public function getMemcachedObj()
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
            $this->_cache_server->quit();
            $this->_cache_server = null;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
