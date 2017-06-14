<?php
// +--------------------------------------------------------------------------------
// | Mini Framework
// +--------------------------------------------------------------------------------
// | Copyright (c) 2015-2017 http://www.sunbloger.com
// +--------------------------------------------------------------------------------
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
// +--------------------------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +--------------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +--------------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +--------------------------------------------------------------------------------

namespace Mini;

abstract class Action
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
     * @var mixed
     */
    protected $_request;
    
    /**
     * 数据库对象池
     * 
     * @var array
     */
    public $_db;
    
    /**
     * Action Instance
     *
     * @var Action
     */
    protected static $_instance;
    
    /**
     * 构造
     * 
     * @param string $controller
     * @param string $action
     * @return Action
     */
    function __construct()
    {
        self::$_instance = $this;
        $this->view = new View();
        $this->params = Params::getInstance();
        $this->_request = Request::getInstance();
        
        if (DB_AUTO_CONNECT === true) {
            $dbConfig = Config::getInstance()->load('database');
            if (is_array($dbConfig)) {
                foreach ($dbConfig as $dbKey => $dbParams) {
                    $this->_db[$dbKey] = Db::factory ('Mysql', $dbParams);
                }
            }
        }
        
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
    protected function assign($variable, $value)
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
    final protected function _forward($action, $controller = null, array $params = null)
    {
        if ($controller !== null) {
            $this->_request->setControllerName($controller);
        }

        $this->_request->setActionName($action);
        
        App::getInstance()->dispatch();
    }
    
    /**
     * 获取Action实例
     * 
     */
    public static function getInstance()
    {
        return self::$_instance;
    }
}
