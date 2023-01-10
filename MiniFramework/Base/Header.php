<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2023 http://www.sunbloger.com
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

class Header
{

    /**
     * Header array
     * 
     * @var array
     */
    private $_headers = [];

    /**
     * 添加一个 Header 如果 name 存在则返回 false
     * 
     * @param string $name
     * @param string $value
     * @return boolean
     */
    public function add(string $name, string $value)
    {
        $name = $this->formatHeaderName($name);
        if (isset($this->_headers[$name])) {
            return false;
        } else {
            $this->_headers[$name] = $value;
        }
        
        return true;
    }

    /**
     * 设置一个 Header 如果 name 存在则覆盖旧的值
     * 
     * @param string $name
     * @param string $value
     * @return boolean
     */
    public function set(string $name, string $value)
    {
        $name = $this->formatHeaderName($name);
        $this->_headers[$name] = $value;
        
        return true;
    }

    /**
     * 移除一个 Header 如果 name 不存在则返回 false
     * 
     * @param string $name
     * @return boolean
     */
    public function remove(string $name)
    {
        $name = $this->formatHeaderName($name);
        if (! isset($this->_headers[$name])) {
            return false;
        } else {
            unset($this->_headers[$name]);
        }
        
        return true;
    }

    /**
     * 判断 Header 是否存在
     * 
     * @param string $name
     * @return boolean
     */
    public function has(string $name)
    {
        $name = $this->formatHeaderName($name);
        
        return isset($this->_headers[$name]);
    }

    /**
     * 获取指定 name 的 Header 如果 name 不存在则返回 false
     * 
     * @param string $name
     * @return boolean
     */
    public function get(string $name)
    {
        $name = $this->formatHeaderName($name);
        if (! isset($this->_headers[$name])) {
            return false;
        }
        
        return $this->_headers[$name];
    }

    /**
     * 获取全部 Header
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->_headers;
    }

    /**
     * 格式化 Header 名称
     * 
     * @param string $name
     * @return string
     */
    public function formatHeaderName($name)
    {
        return str_replace(' ', '-', ucwords(strtolower(str_replace('-', ' ', $name))));
    }
}
