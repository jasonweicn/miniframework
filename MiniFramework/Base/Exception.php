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

class Exception extends \Exception
{

    /**
     * 构造
     *
     * @param string $message 错误信息
     * @param int $code 错误代码
     */
    public function __construct($message, $code = 0, $level = Log::ERROR, $position = null)
    {
        parent::__construct($message, $code);
    }

    /**
     * 自定义异常处理方法
     *
     * @param \Throwable $e
     */
    public static function customExcepion(\Throwable $e)
    {
        $code = 500;
        if (Response::getInstance()->getStatusMsg($e->getCode()) !== false) {
            $code = $e->getCode();
        }
        Log::record(str_replace(["\r\n", "\r", "\n"], ' ', $e->__toString()), Log::ERROR, ['file' => $e->getFile(), 'line' => $e->getLine()]);
        self::showError([
            'level'     => 'ERROR',
            'message'   => $e->getMessage(),
            'code'      => $code == 0 ? 500 : $code,
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'trace'     => $e->getTraceAsString()
        ]);
    }

    /**
     * 自定义错误处理方法
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     */
    public static function customError($level, $message, $file, $line)
    {
        $error = [
            'message' => $message,
            'file' => $file,
            'line' => $line
        ];
        switch ($level) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                Log::record($message, Log::ERROR, ['file' => $file, 'line' => $line]);
                $error['level'] = Log::ERROR;
                self::showError($error);
                die();
                break;
            case E_WARNING:
                Log::record($message, Log::WARNING, ['file' => $file, 'line' => $line]);
                $error['level'] = Log::WARNING;
                self::showWarning($error);
                break;
            default:
                Log::record($message, Log::NOTICE, ['file' => $file, 'line' => $line]);
                $error['level'] = Log::NOTICE;
                self::showWarning($error);
                break;
        }
    }

    /**
     * 输出警告
     * 
     * @param array $error
     */
    public static function showWarning($error = [])
    {
        $isCli = preg_match("/cli/i", PHP_SAPI) ? true : false;
        if (SHOW_ERROR === true) {
            if (! empty($error) && is_array($error)) {
                if ($isCli) {
                    $body = "{$error['level']}: {$error['message']} in {$error['file']} on line {$error['line']}\n";
                } else {
                    $body = "<p><b>{$error['level']}</b>: {$error['message']} in <b>{$error['file']}</b> on line <b>{$error['line']}</b></p>\n";
                }
                echo $body;
            }
        }
    }

    /**
     * 输出错误
     *
     * @param array $error
     */
    public static function showError($error = [])
    {
        $isCli = preg_match("/cli/i", PHP_SAPI) ? true : false;
        if ($isCli) {
            echo "{$error['level']}: {$error['message']} in {$error['file']} on line {$error['line']}\n";
            die();
        }
        if (SHOW_ERROR === true) {
            $response = Response::getInstance();
            $app = \Mini\Base\App::getInstance();
            if ($app->isApi === true) {
                $json = self::toJson($error);
                $response->type('json')->httpStatus($error['code'])->send($json);
            } else {
                $body = self::toHtml($error);
                $response->type('html')->httpStatus($error['code'])->send($body);
            }
        } else {
            self::showErrorPage($error['code'], $error);
        }
    }

    /**
     * 显示自定义的报错内容
     *
     * @param int $code
     */
    public static function showErrorPage($code, $error = null)
    {
        $customErrorPage = false;
        if (! empty(ERROR_PAGE) && substr_count(ERROR_PAGE, '/') == 1) {
            $customErrorPage = true;
        }
        if ($customErrorPage === true) {
            $r = explode('/', ERROR_PAGE);
            $controller = $r[0];
            $action = $r[1];
            $app = App::getInstance();
            if ($action == $app->action) {
                if ($controller === null || $controller == $app->controller) {
                    return false;
                }
            }
            if ($controller !== null) {
                $app->setController($controller);
            }
            $app->setAction($action)->dispatch($error);
            die();
        } else {
            $response = Response::getInstance();
            $status = $response->getStatusMsg($code);
            if ($status === false) {
                $code = 500;
                $status = $response->getStatusMsg($code);
            }
            $info = '<html><head><title>An error occurred</title></head><body><h1>An error occurred</h1>';
            $info .= '<h2>' . $code . ' ' . $status . '</h2>';
            $info .= '<hr><p>Powered by MiniFramework. ' . date('r') . '</p></body></html>';
            $response->type('html')->httpStatus($code)->send($info);
            die();
        }
    }
    
    /**
     * 错误信息转为 JSON 格式
     * 
     * @param array $error
     * @return string|boolean
     */
    public static function toJson($error)
    {
        $data = [
            'code' => $error['code'],
            'message' => $error['message'],
            'position' => [
                'file' => $error['file'],
                'line' => $error['line']
            ],
            'trace' => explode("\n", $error['trace'])
        ];
        
        return pushJson($data, false);
    }
    
    /**
     * 错误信息转为 HTML 格式
     * 
     * @param array $error
     * @return string
     */
    public static function toHtml($error)
    {
        $html = "<html>\n";
        $html.= "<head><title>An error occurred</title></head>\n";
        $html.= "<body>\n";
        if (empty($error) || !is_array($error)) {
            $html .= "<h1>An error occurred</h1>\n";
        } else {
            $html .= "<p><b>{$error['level']}</b>: {$error['message']}</p>\n";
            $html .= "<p><b>Position</b>: {$error['file']} (line {$error['line']})</p>\n";
            if (isset($error['trace']) && ! empty($error['trace'])) {
                $html .= "<p><b>Stack trace</b>: <br>\n" . str_replace("\n", "<br>\n", $error['trace']) . "</p>\n";
            }
            if ($error['level'] != Log::WARNING && $error['level'] != log::NOTICE) {
                $html .= "<hr><p>Powered by MiniFramework. " . date('r') . "</p>\n";
            }
        }
        $html .= '</body></html>';
        
        return $html;
    }
}
