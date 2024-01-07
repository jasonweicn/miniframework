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
     * View层变量数组
     * @var array
     */
    private array $variables = [];

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

        $this->assign('baseUrl', $this->baseUrl());

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
        $this->variables[$variable] = $value;
        
        return true;
    }

    public function __get($variable)
    {
        return isset($this->variables[$variable]) ? $this->variables[$variable] : null;
    }

    /**
     * 接收来自于控制器的变量
     *
     * @param string $variable
     * @param mixed $value
     */
    public function assign($variable, $value)
    {
        return $this->__set($variable, $value);
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
        $response = Response::getInstance();
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
            $response->httpStatus(200)->send($finalViewPage);
        } else {
            $response->httpStatus(200)->send($content);
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

        if (TPL_ON === true) {

            // 模板缓存key
            $tplCacheKey = md5($script);

            // 模板缓存文件
            $tplCacheFile = CACHE_PATH . '/tpl_' . $tplCacheKey . '.php';

            // 检查是否需要刷新模板缓存
            $refreshCache = true;
            if (file_exists($tplCacheFile)) {
                $cacheTime = filemtime($tplCacheFile);
                $scriptTime = filemtime($script);
                if ($cacheTime > $scriptTime) {
                    $refreshCache = false;
                }
            }

            // 刷新模板缓存
            if ($refreshCache === true) {
                $tplContent = file_get_contents($script);
                is_dir(CACHE_PATH) or mkdir(CACHE_PATH, 0700, true);
                if (! file_exists($tplCacheFile)) {
                    file_put_contents($tplCacheFile, $this->compiler($tplContent));
                } else {
                    if (is_writable($tplCacheFile)) {
                        file_put_contents($tplCacheFile, $this->compiler($tplContent));
                    } else {
                        throw new Exception('Failed to write "' . $tplCacheFile . '", Permission denied.', 500);
                    }
                }
            }

            $script = $tplCacheFile;
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
    public function endBlock($blockName = null)
    {
        if ($this->_blockStatus == 0) {
            return false;
        }
        $block = ob_get_clean();
        if (! isset($blockName)) {
            end($this->_blockData);
            $blockName = key($this->_blockData);
            if ($blockName == null) {
                return false;
            }
        }
        $this->_blockData[$blockName] = $block;
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
        if (! isset($jsFileUrl) || empty($jsFileUrl)) {
            return false;
        }
        $this->_jsFile[] = $jsFileUrl;

        return true;
    }

    /**
     * 模板引擎编译器
     *
     * @param string $content
     * @return string
     */
    public function compiler($content)
    {
        $reg = "/\\" . TPL_SEPARATOR_L . "(.*?)\\" . TPL_SEPARATOR_R . "/";
        $content = preg_replace_callback($reg, [
            $this,
            'parseGeneralTag'
        ], $content);

        return $content;
    }

    /**
     * 解析普通标记
     *
     * @param array $tag
     * @return mixed|string
     */
    private function parseGeneralTag($matches)
    {
        if (isset($matches[1])) {
            $tagString = $matches[1];
        }
        if ('$' == substr($tagString, 0, 1)) { // 变量
            $variable = substr($tagString, 1);
            if (isset($this->$variable)) {
                return '<?php echo $this->' . $variable . '; ?>';
            } elseif (strpos($variable, '.') !== false) { // 对象或数组
                $names = explode('.', $variable);
                $variable = array_shift($names);
                if (is_object($this->$variable)) { // 对象
                    foreach ($names as $val) {
                        $variable .= '->' . $val;
                    }
                } else { // 数组
                    foreach ($names as $val) {
                        $variable .= '["' . $val . '"]';
                    }
                }
                return '<?php echo $this->' . $variable . '; ?>';
            }
        } elseif ('const:' == substr($tagString, 0, 6)) { // 常量
            $constName = substr($tagString, 6);
            if (defined($constName)) {
                return constant($constName);
            }
        } elseif ('layout:' == substr($tagString, 0, 7)) { // 加载布局
            $layoutName = substr($tagString, 7);
            if (isset($this->_layout->$layoutName) || $layoutName == 'content') {
                return '<?php echo $this->_layout->' . $layoutName . '; ?>';
            } else {
                return '<?php echo $this->render(LAYOUT_PATH . "/' . $layoutName . '.php"); ?>';
            }
        } elseif ('beginBlock:' == substr($tagString, 0, 11)) { // 开始代码块
            $blockName = substr($tagString, 11);
            return '<?php $this->beginBlock("' . $blockName . '"); ?>';
        } elseif ('endBlock:' == substr($tagString, 0, 9)) { // 结束代码块
            $blockName = substr($tagString, 9);
            return '<?php $this->endBlock("' . $blockName . '"); ?>';
        } elseif ('endBlock' == $tagString) { // 结束代码块，简写方式
            return '<?php $this->endBlock(); ?>';
        } elseif ('insertBlock:' == substr($tagString, 0, 12)) { // 插入代码块
            $blockName = substr($tagString, 12);
            return '<?php $this->insertBlock("' . $blockName . '"); ?>';
        }

        return TPL_SEPARATOR_L . $tagString . TPL_SEPARATOR_R;
    }
}
