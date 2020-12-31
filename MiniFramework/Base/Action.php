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

class Action
{

    /**
     * View实例
     *
     * @var View
     */
    protected $view;

    /**
     * Params实例
     *
     * @var Params
     */
    protected $params;

    /**
     * Request实例
     *
     * @var Request
     */
    protected $_request;

    /**
     * 构造
     *
     * @param string $controller            
     * @param string $action            
     * @return Action
     */
    function __construct()
    {
        $this->view = new View();
        $this->params = Params::getInstance();
        $this->_request = Request::getInstance();
        
        if (method_exists($this, '_init')) {
            $this->_init();
        }
    }

    /**
     * 向View传入变量
     *
     * @param mixed $variable            
     * @param mixed $value            
     */
    final protected function assign($variable, $value)
    {
        $this->view->assign($variable, $value);
    }

    /**
     * 转至给定的控制器和动作
     *
     * @param string $action            
     * @param string $controller            
     * @param array $params            
     */
    final protected function _forward($action, $controller = null, array $params = NULL)
    {
        if ($controller !== null) {
            $this->_request->setControllerName($controller);
        }
        
        $this->_request->setActionName($action);
        
        App::getInstance()->dispatch();
    }
}
