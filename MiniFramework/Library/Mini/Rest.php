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

namespace Mini;

class Rest
{
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
     * Http实例
     * 
     * @var Http
     */
    protected $http;
    
    /**
     * 数据库对象池
     * 
     * @var array
     */
    public $_db;
    
    /**
     * Rest Instance
     *
     * @var Rest
     */
    protected static $_instance;
    
    /**
     * 构造
     */
    function __construct()
    {
        self::$_instance = $this;
        $this->params = Params::getInstance();
        $this->_request = Request::getInstance();
        $this->http = Http::getInstance();
        
        $requestMethod = $this->_request->method();
        
        if ($requestMethod == 'POST') {
            $this->params->setParams($this->params->_post);
        } elseif ($requestMethod == 'PUT') {
            parse_str(file_get_contents('php://input'), $arguments);
            if (!empty($arguments)) {
                $this->params->setParams($arguments);
            }
        }
        
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
        
        $requestMethod = strtolower($requestMethod);
        
        if (method_exists($this, $requestMethod)) {
            $this->$requestMethod();
        }
    }
    
    /**
     * 发送JSON
     * 
     * @param int $code HTTP状态码
     * @param string $msg 服务器返回给客户端的消息
     * @param string $data 返回的数据
     */
    public function responseJson($code = 200, $msg = '', $data = null)
    {
        if ($msg == '') {
            $msg = Http::isStatus($code) === false ? '' : Http::isStatus($code);
        }
        
        $content = array(
                'code'  => $code,
                'msg'   => $msg,
                'data'  => $data
        );
        $json = pushJson($content, false);
        
        $this->http->header('Content-Type', 'application/json')->response($code, $json);
    }
    
    /**
     * 发送XML
     * @param int $code HTTP状态码
     * @param string $msg 服务器返回给客户端的消息
     * @param string $data 返回的数据
     */
    public function responseXml($code = 200, $msg = '', $data = array())
    {
        if ($msg == '') {
            $msg = Http::isStatus($code) === false ? '' : Http::isStatus($code);
        }
        
        $xml = pushXml($data, false, false, 'data', array(
                'code'  => $code,
                'msg'   => $msg
        ));
        
        $this->http->header('Content-Type', 'application/xml')->response($code, $xml);
    }
    
    /**
     * 获取实例
     * 
     */
    public static function getInstance()
    {
        return self::$_instance;
    }
}
