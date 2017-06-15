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

namespace Mini\Cache;

abstract class Cache_Abstract
{
    /**
     * Exceptions实例
     * @var Exceptions
     */
    protected $_exception;
    
    /**
     * 数据库连接参数
     * 
     * @var array
     */
    protected $_params = array();
    
    /**
     * 缓存过期时间（单位：秒）
     * 
     * @var int
     */
    protected $_expire = 1800;
    
    /**
     * 是否压缩缓存数据
     * @var bool
     */
    protected $_compress_flag = true;
    
    /**
     * 缓存服务器
     * 
     * @var object | resource | null
     */
    protected $_cache_server = null;
    
    /**
     * 读取缓存
     * 
     * @param string $name
     */
    abstract protected function get($name);
    
    /**
     * 写入缓存
     * 
     * @param string $name
     * @param mixed $value
     * @param int $expire
     */
    abstract protected function set($name, $value, $expire = null);
    
    /**
     * 清除缓存
     * 
     * @param string $name
     */
    abstract protected function del($name);
    
    /**
     * 构造
     * 
     * @param array $params => array (
     *     host     => (string) 主机，默认值为空
     *     port     => (int) 端口，默认值为空
     *     
     *     prefix   => (string) 缓存名前缀，默认值为空
     * )
     * @return Cache_Abstract
     */
    public function __construct($params)
    {
        if (!is_array($params)) {
            throw new Exceptions('Adapter params must be in an array.');
        }
        
        $this->_params = $params;
    }
    
    public function setCompress ($flag = false)
    {
        $this->_compress_flag = $flag;
    }
}
