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

/**
 * MiniFramework引导程序
 */

// 系统目录分隔符
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// MiniFramework 核心文件路径
defined('MINI_PATH') or define('MINI_PATH', dirname(__FILE__));

// 类库路径
defined('LIB_PATH') or  define('LIB_PATH', MINI_PATH . DS . 'Library');

// 应用路径
defined('APP_PATH') or define('APP_PATH', dirname(dirname($_SERVER['SCRIPT_FILENAME'])));

// 应用命名空间名称
defined('APP_NAMESPACE') or define('APP_NAMESPACE', 'App');

// 是否显示错误信息
defined('SHOW_ERROR') or define('SHOW_ERROR', false);

// 是否显示开发者调试信息
defined('SHOW_DEBUG') or define('SHOW_DEBUG', true);

// 缓存路径
defined('CACHE_PATH') or define('CACHE_PATH', APP_PATH . DS . 'Cache');

// 配置文件路径
defined('CONFIG_PATH') or define('CONFIG_PATH', APP_PATH . DS . 'Config');

// 布局功能开关
defined('LAYOUT_ON') or define('LAYOUT_ON', false);

// 布局文件路径
defined('LAYOUT_PATH') or define('LAYOUT_PATH', APP_PATH . DS . 'Layout');

// HTTP缓存
defined('HTTP_CACHE_CONTROL') or define('HTTP_CACHE_CONTROL', 'private');

// 数据库自动连接
defined('DB_AUTO_CONNECT') or define('DB_AUTO_CONNECT', false);

// REST接口功能开关（提示：开启后，原有使用Api命名的Controller将会失效）
defined('REST_ON') or define('REST_ON', false);

if (SHOW_ERROR === true) {
    ini_set('display_errors', 'On');
} else {
    ini_set('display_errors', 'Off');
}

require(LIB_PATH . DS . 'Mini' . DS . 'Loader.php');
Mini\Loader::getInstance();

//一切由此开始
Mini\App::getInstance()->run();
