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
            throw new Exceptions('View "' . $this->_action . '" does not exist.', 404);
        }
        
        $content = $this->render($view);
        header('Cache-Control: ' . HTTP_CACHE_CONTROL);
        header('X-Powered-By: MiniFramework');
        
        if (LAYOUT_ON === true && $this->_layout->getLayout()) {
            $this->_layout->content = $content;
            $layoutScript = $this->_layout->getLayoutScript();
            include ($layoutScript);
        } else {
            echo $content;
        }
        
        die();
    }

    /**
     * 渲染器
     *
     * @param string $script            
     * @param bool $check
     *            (true | false)
     * @return string
     */
    final public function render($script, $check = true)
    {
        if ($check === true) {
            if (! file_exists($script)) {
                throw new Exceptions('File "' . $script . '" does not exist.', 404);
            }
        }
        
        ob_start();
        include ($script);
        $content = ob_get_contents();
        ob_end_clean();
        ob_start();
        
        return $content;
    }
}
