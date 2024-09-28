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

/**
 * MiniFramework引导程序
 */

// 系统目录分隔符
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// MiniFramework 核心文件路径
defined('MINI_PATH') || define('MINI_PATH', __DIR__);

// 应用路径
defined('APP_PATH') || define('APP_PATH', dirname(dirname($_SERVER['SCRIPT_FILENAME'])));

// 应用命名空间名称
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

// 应用运行环境
defined('APP_ENV') || define('APP_ENV', 'prod');

// 伪静态扩展名
defined('URL_SUFFIX') || define('URL_SUFFIX', 'htm|html|shtml'); 

// URL（伪静态）分割符号
defined('URL_SPLIT_SYMBOL') || define('URL_SPLIT_SYMBOL', '_');

// 自定义错误页面，留空将显示框架默认错误页（格式：controller/action）
defined('ERROR_PAGE') || define('ERROR_PAGE', '');

// 是否显示错误信息（值为 false 时，将显示 ERROR_PAGE 定义的错误页面）
defined('SHOW_ERROR') || define('SHOW_ERROR', false);

// 是否显示开发者调试信息
defined('SHOW_DEBUG') || define('SHOW_DEBUG', true);

// 缓存路径
defined('CACHE_PATH') || define('CACHE_PATH', APP_PATH . DS . 'Cache');

// 配置文件路径
defined('CONFIG_PATH') || define('CONFIG_PATH', APP_PATH . DS . 'Config');

// 布局功能开关
defined('LAYOUT_ON') || define('LAYOUT_ON', false);

// 布局文件路径
defined('LAYOUT_PATH') || define('LAYOUT_PATH', APP_PATH . DS . 'Layout');

// WEB站点根目录对应的路径
defined('PUBLIC_PATH') || define('PUBLIC_PATH', dirname($_SERVER['SCRIPT_FILENAME']));

// HTTP缓存
defined('HTTP_CACHE_CONTROL') || define('HTTP_CACHE_CONTROL', 'private');

// 数据库自动连接
defined('DB_AUTO_CONNECT') || define('DB_AUTO_CONNECT', false);

// REST接口功能开关（提示：开启后，原有使用Api命名的Controller将会失效）
defined('REST_ON') || define('REST_ON', false);

// 是否开启日志
defined('LOG_ON') || define('LOG_ON', false);

// 日志存储模式（1为文件，2为数据库）
defined('LOG_MODE') || define('LOG_MODE', 1);

// 日志记录等级
defined('LOG_LEVEL') || define('LOG_LEVEL', 'EMERG,ALERT,CRIT,ERROR,WARNING,NOTICE,INFO,DEBUG,SQL');

// 日志存储路径（LOG_MODE为1时生效）
defined('LOG_PATH') || define('LOG_PATH', APP_PATH . DS . 'Log');

// 日志文件存储的最大数量（值为0时允许文件数量无限增长，LOG_MODE为1时生效）
defined('LOG_MAX_FILES') || define('LOG_MAX_FILES', 30);

// 日志存储的数据库配置（LOG_MODE为2时生效）
defined('LOG_DB_CONFIG') || define('LOG_DB_CONFIG', 'database:default');

// 日志存储的数据表名（LOG_MODE为2时生效）
defined('LOG_TABLE_NAME') || define('LOG_TABLE_NAME', 'log');

// CSRF令牌功能开关
defined('CSRF_TOKEN_ON') || define('CSRF_TOKEN_ON', false);

// CSRF令牌获得方式 cookie | header
defined('CSRF_TYPE') || define('CSRF_TYPE', 'cookie');

// 模板功能开关
defined('TPL_ON') || define('TPL_ON', false);

// 模板引擎标记符号
defined('TPL_SEPARATOR_L') || define('TPL_SEPARATOR_L', '{');
defined('TPL_SEPARATOR_R') || define('TPL_SEPARATOR_R', '}');

require (MINI_PATH . DS . 'Base' . DS . 'Loader.php');
Mini\Base\Loader::getInstance();

// 一切由此开始
Mini\Base\App::getInstance()->run();
