<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2024 http://www.sunbloger.com
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

class Layout
{

    /**
     * 布局变量容器
     *
     * @var array
     */
    protected $_container;

    /**
     * 布局文件路径
     *
     * @var mixed
     */
    private $_layoutPath;

    /**
     * 布局名称
     *
     * @var string
     */
    protected $_layout;
    
    /**
     * 克隆
     */
    private function __clone()
    {}
    
    public function __isset($key)
    {
        return isset($this->_container[$key]);
    }

    public function __set($key, $value)
    {
        $this->_container[$key] = $value;
    }

    public function __get($key)
    {
        if (isset($this->_container[$key])) {
            return $this->_container[$key];
        }

        return null;
    }

    /**
     * 设置布局文件所在路径
     *
     * @param string $path 布局文件存放路径
     */
    public function setLayoutPath(string $path)
    {
        $this->_layoutPath = $path;

        return $this;
    }

    /**
     * 获取布局文件所在路径
     */
    public function getLayoutPath()
    {
        return $this->_layoutPath;
    }

    /**
     * 设置布局
     * 
     * @param string $name 布局名称
     * @return \Mini\Base\Layout
     */
    public function setLayout($name = null)
    {
        if ($name == null) {
            $this->_layout = null;
        } else {
            if (! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $name)) {
                throw new Exception('Layout "' . $name . '"  invalid.');
            }
            $this->_layout = (string) $name;
        }
        
        return $this;
    }

    /**
     * 获取布局
     *
     * @param string $name
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * 获取布局脚本
     *
     * @param string $layoutScript
     */
    public function getLayoutScript()
    {
        $layoutScript = $this->getLayoutPath() . DS . $this->getLayout() . '.php';
        if (! file_exists($layoutScript)) {
            throw new Exception('Layout "' . $this->getLayout() . '" does not exist.');
        }

        return $layoutScript;
    }
}
