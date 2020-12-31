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

class File extends Cache_Abstract
{

    /**
     * 写入缓存
     *
     * @param string $name
     * @param mixed $value
     * @param int $expire
     */
    public function set($name, $value, $expire = null)
    {
        if (! isset($expire) || empty($expire)) {
            $expire = 0;
        }
        $cache_key = $this->getCacheKey($name);
        $cache_value = serialize($value);
        $compress_flag = 0;
        if ($this->_compress_flag && function_exists('gzcompress')) {
            $cache_value = gzcompress($cache_value);
            $compress_flag = 1;
        }
        $cache_value = sprintf('%012d', $expire) . $compress_flag . $cache_value;
        
        if (! file_exists(CACHE_PATH) && ! is_dir(CACHE_PATH)) {
            mkdir(CACHE_PATH, 0744, true);
        }
        file_put_contents(CACHE_PATH . DS . $cache_key, $cache_value);
    }

    /**
     * 读取缓存
     *
     * @param string $name            
     */
    public function get($name)
    {
        $cache_key = $this->getCacheKey($name);
        $cache_file = CACHE_PATH . DS . $cache_key;
        if (! file_exists($cache_file)) {
            return false;
        }
        $cache_value = file_get_contents($cache_file);
        
        if ($cache_value === false) {
            return false;
        }
        
        $expire = (int) substr($cache_value, 0, 12);
        if ($expire != 0 && time() > filemtime($cache_file) + $expire) {
            unlink($cache_file);
            return false;
        }
        
        $compress_flag = (int) substr($cache_value, 12, 1);
        if ($compress_flag == 1 && function_exists('gzcompress')) {
            $value = unserialize(gzuncompress(substr($cache_value, 13)));
        } else {
            $value = unserialize(substr($cache_value, 13));
        }
        
        return $value;
    }

    /**
     * 删除缓存
     *
     * @param string $name            
     */
    public function del($name)
    {
        $cache_key = $this->getCacheKey($name);
        $cache_file = CACHE_PATH . DS . $cache_key;
        @unlink($cache_file);
    }

    /**
     * 获取缓存文件KEY值
     *
     * @param string $name            
     * @return string
     */
    private function getCacheKey($name)
    {
        return md5($name);
    }
}
