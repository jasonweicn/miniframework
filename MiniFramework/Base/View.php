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
namespace Mini\Base;

class View
{

    /**
     * 控制器
     *
     * @var string
     */
    private $_controller;

    /**
     * 动作
     *
     * @var string
     */
    private $_action;

    /**
     * Request实例
     *
     * @var Request
     */
    private $_request;

    /**
     * 基础路径
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * Layout实例
     *
     * @var Layout
     */
    public $_layout;
    
    /**
     * 代码块数组
     * 
     * @var array
     */
    private $_blockData = [];
    
    /**
     * 代码块开启状态
     * 
     * @var int
     */
    private $_blockStatus = 0;
    
    /**
     * JS文件数组
     * 
     * @var array
     */
    private $_jsFile = [];

    /**
     * 构造
     */
    function __construct()
    {
        $this->_request = Request::getInstance();
        $app = App::getInstance();
        $this->_controller = $app->controller;
        $this->_action = $app->action;
        
        if (LAYOUT_ON === true) {
            $this->_layout = Layout::getInstance();
            $this->_layout->setLayoutPath(LAYOUT_PATH);
        }
    }

    /**
     * 获取基础路径
     */
    public function baseUrl()
    {
        if ($this->_baseUrl === null) {
            $this->_baseUrl = $this->_request->getBaseUrl();
        }
        return $this->_baseUrl;
    }

    public function __set($variable, $value)
    {
        $this->assign($variable, $value);
    }

    /**
     * 接收来自于控制器的变量
     *
     * @param string $variable            
     * @param mixed $value            
     */
    public function assign($variable, $value)
    {
        if (substr($variable, 0, 1) != '_') {
            $this->$variable = $value;
            return true;
        }
        return false;
    }

    /**
     * 显示
     */
    final public function display()
    {
        $view = APP_PATH . DS . 'View' . DS;
        $view .= strtolower($this->_controller) . DS . $this->_action . '.php';
        
        if (! file_exists($view)) {
            throw new Exception('View "' . $this->_action . '" does not exist.', 404);
        }
        
        $content = $this->render($view);
        
        $_http = Http::getInstance();
        
        if (LAYOUT_ON === true && $this->_layout->getLayout()) {
            $this->_layout->content = $content;
            $layoutScript = $this->_layout->getLayoutScript();
            $finalViewPage = $this->render($layoutScript);
            if (! empty($this->_jsFile)) {
                foreach ($this->_jsFile as $url) {
                    $js = '<script src="' . $url . '"></script>';
                    $finalViewPage = str_replace('</body>', $js . "\n</body>", $finalViewPage);
                }
            }
            $_http->response(200, $finalViewPage);
        } else {
            $_http->response(200, $content);
        }
        
        die();
    }

    /**
     * 渲染器
     * 
     * @param string $script
     * @param boolean $check
     * @throws Exception
     * @return string
     */
    final public function render($script, $check = true)
    {
        if ($check === true) {
            if (! file_exists($script)) {
                throw new Exception('File "' . $script . '" does not exist.', 404);
            }
        }
        
        if (SHOW_DEBUG === false) {
            ob_end_clean();
        }
        
        ob_start();
        include ($script);
        $content = ob_get_contents();
        ob_end_clean();
        ob_start();
        
        return $content;
    }
    
    /**
     * 开启一个代码块
     * 
     * @param string $blockName
     * @return boolean
     */
    public function beginBlock($blockName)
    {
        if ($this->_blockStatus == 1) {
            return false;
        }
        $this->_blockStatus = 1;
        $this->_blockData[$blockName] = '';
        ob_start();
        ob_implicit_flush(false);
        
        return true;
    }
    
    /**
     * 结束当前的代码块
     * 
     * @return boolean
     */
    public function endBlock()
    {
        if ($this->_blockStatus == 0) {
            return false;
        }
        $block = ob_get_clean();
        end($this->_blockData);
        $lastKey = key($this->_blockData);
        if ($lastKey == null) {
            return false;
        }
        $this->_blockData[$lastKey] = $block;
        $this->_blockStatus = 0;
        
        return true;
    }
    
    /**
     * 插入代码块
     * 
     * @param string $blockName
     * @return boolean
     */
    public function insertBlock($blockName)
    {
        if (! isset($this->_blockData[$blockName])) {
            return false;
        }
        echo $this->_blockData[$blockName];
        
        return true;
    }
    
    /**
     * 设置JS文件资源的引入
     * 
     * @param string $jsFileUrl
     * @return boolean
     */
    public function setJsFile($jsFileUrl)
    {
        if (!isset($jsFileUrl) || empty($jsFileUrl)) {
            return false;
        }
        $this->_jsFile[] = $jsFileUrl;
        
        return true;
    }
}
