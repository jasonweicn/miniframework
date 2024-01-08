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

use Mini;

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
     * Request实例（旧名称）
     *
     * @var Mini\Base\Request
     */
    protected $_request;

    /**
     * Request实例（新名称）
     * 
     * @var Mini\Base\Request
     */
    protected $request;

    /**
     * Response实例
     *  
     * @var Mini\Base\Response
     */
    protected $response;

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
        $this->request = $this->_request = Request::getInstance();
        $this->response = Response::getInstance();
        
        if (method_exists($this, '_init')) {
            $this->_init();
        }
    }

    /**
     * 析构
     */
    function __destruct() {
        if (method_exists($this, '_end')) {
            $this->_end();
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
     * 转至给定的控制器和动作（旧名称）
     *
     * @param string $action
     * @param string $controller
     */
    final protected function _forward($action, $controller = null)
    {
        $this->forward($action, $controller);
    }

    /**
     * 转至给定的控制器和动作
     * 
     * @param string $action
     * @param string $controller
     */
    final protected function forward($action, $controller = null)
    {
        $app = App::getInstance();
        if ($action == $app->action) {
            if ($controller === null || $controller == $app->controller) {
                return false;
            }
        }
        if ($controller !== null) {
            $app->setController($controller);
        }
        $app->setAction($action)->dispatch();
        die();
    }

    /**
     * 显性跳转
     * 
     * @param string $action
     * @param string $controller
     */
    final protected function redirect(string $action, string $controller = null)
    {
        $baseUrl = $this->request->getBaseUrl();
        $redirectUrl = $baseUrl;
        if ($controller !== null) {
            $redirectUrl .= '/' . $controller;
        } else {
            $redirectUrl .= '/' . App::getInstance()->controller;
        }
        $redirectUrl .= '/' . $action;
        header('Location:' . $redirectUrl);
        die();
    }
}
