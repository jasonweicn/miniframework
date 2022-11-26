<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2022 http://www.sunbloger.com
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
     * 重构 toString
     */
    public function __toString()
    {
        if (SHOW_ERROR === true) {
            return parent::__toString();
        } else {
            self::showErrorPage($this->code);
        }
    }

    /**
     * 自定义异常处理方法
     *
     * @param \Throwable $e
     */
    public static function customExcepion(\Throwable $e)
    {
        Log::record(str_replace(["\r\n", "\r", "\n"], ' ', $e->__toString()), Log::ERROR, ['file' => $e->getFile(), 'line' => $e->getLine()]);
        self::showError([
            'level'     => 'ERROR',
            'message'   => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'trace'     => $e->getTraceAsString()
        ], true);
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
                self::showError($error, true);
                die();
                break;
            case E_WARNING:
                Log::record($message, Log::WARNING, ['file' => $file, 'line' => $line]);
                $error['level'] = Log::WARNING;
                self::showError($error);
                break;
            default:
                Log::record($message, Log::NOTICE, ['file' => $file, 'line' => $line]);
                $error['level'] = Log::NOTICE;
                self::showError($error);
                break;
        }
    }

    /**
     * 输出错误
     *
     * @param array $error
     * @param boolean $fatal
     */
    public static function showError($error = [], $fatal = false)
    {
        if (SHOW_ERROR === true) {
            if (! empty($error) && is_array($error)) {
                $isCli = preg_match("/cli/i", PHP_SAPI) ? true : false;
                if ($isCli) {
                    $body = "{$error['level']}: {$error['message']} in {$error['file']} on line {$error['line']}\n";
                } else {
                    $body = "<p><b>{$error['level']}</b>: {$error['message']} in <b>{$error['file']}</b> on line <b>{$error['line']}</b></p>\n";
                    if (isset($error['trace']) && ! empty($error['trace'])) {
                        $body .= "<p><b>Stack trace</b>: \n" . $error['trace'] . "</p>";
                    }
                }
                echo $body;
            }
        } else {
            if ($fatal === true) {
                self::showErrorPage(500);
            }
        }
    }

    /**
     * 显示自定义的报错内容
     *
     * @param int $code
     */
    public static function showErrorPage($code)
    {
        $response = Response::getInstance();
        $status = $response->getStatusMsg($code);
        if ($status === false) {
            $code = 500;
            $response->getStatusMsg($code);
        }
        $info = '<html><head><title>Error</title></head><body><h1>An error occurred</h1>';
        $info .= '<h2>' . $code . ' ' . $status . '</h2></body></html>';
        $response->header('Content-Type', 'text/html; charset=utf-8')->httpStatus($code)->send($info);

        die();
    }
}
