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
            if (! empty($arguments)) {
                $this->params->setParams($arguments);
            }
        }

        if (method_exists($this, '_init')) {
            $this->_init();
        }

        $requestMethod = strtolower($requestMethod);

        if (method_exists($this, $requestMethod)) {
            $this->$requestMethod();
        } else {
            $this->forbidden();
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
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
        $json = pushJson($content, false);

        $headers = $this->_request->getHeaders();
        if (isset($headers['Ver']) && preg_match("/^\d+$/", $headers['Ver'])) {
            $this->http->header('X-Api-Ver', $headers['Ver']);
        }

        $this->http->header('Content-Type', 'application/json')->response($code, $json);
    }

    /**
     * 发送XML
     *
     * @param int $code HTTP状态码
     * @param string $msg 服务器返回给客户端的消息
     * @param string $data 返回的数据
     */
    public function responseXml($code = 200, $msg = '', $data = [])
    {
        if ($msg == '') {
            $msg = Http::isStatus($code) === false ? '' : Http::isStatus($code);
        }

        $xml = pushXml($data, false, false, 'data', [
            'code' => $code,
            'msg' => $msg
        ]);

        $headers = $this->_request->getHeaders();
        if (isset($headers['Ver']) && preg_match("/^\d+$/", $headers['Ver'])) {
            $this->http->header('X-Api-Ver', $headers['Ver']);
        }

        $this->http->header('Content-Type', 'application/xml')->response($code, $xml);
    }

    /**
     * 禁止访问（输出403）
     */
    public function forbidden()
    {
        $this->http->response(403, '403 - Forbidden');
    }

    /**
     * 默认的GET方法
     */
    public function get()
    {
        $this->forbidden();
    }

    /**
     * 默认的POST方法
     */
    public function post()
    {
        $this->forbidden();
    }

    /**
     * 默认的PUT方法
     */
    public function put()
    {
        $this->forbidden();
    }

    /**
     * 默认的DELETE方法
     */
    public function delete()
    {
        $this->forbidden();
    }

    /**
     * 获取实例
     *
     * @return Rest
     */
    public static function getInstance()
    {
        return self::$_instance;
    }
}
