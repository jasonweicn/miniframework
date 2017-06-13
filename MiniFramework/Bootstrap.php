<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

// MiniFramework 核心文件路径
if (!defined('MINI_PATH'))          define('MINI_PATH',             dirname(__FILE__));

// 应用路径
if (!defined('APP_PATH'))           define('APP_PATH',              dirname(dirname($_SERVER['SCRIPT_FILENAME'])));

// 应用命名空间名称
if (!defined('APP_NAMESPACE'))      define('APP_NAMESPACE',         'App');

// 是否显示错误信息
if (!defined('SHOW_ERROR'))         define('SHOW_ERROR',            false);

// 是否显示开发者调试信息
if (!defined('SHOW_DEBUG'))         define('SHOW_DEBUG',            true);

// 缓存路径
if (!defined('CACHE_PATH'))         define('CACHE_PATH',            APP_PATH . DIRECTORY_SEPARATOR . 'Cache');

// 配置文件路径
if (!defined('CONFIG_PATH'))        define('CONFIG_PATH',           APP_PATH . DIRECTORY_SEPARATOR . 'Config');

// 布局功能开关
if (!defined('LAYOUT_ON'))          define('LAYOUT_ON',             false);

// 布局文件路径
if (!defined('LAYOUT_PATH'))        define('LAYOUT_PATH',           APP_PATH . DIRECTORY_SEPARATOR . 'Layout');

// HTTP缓存
if (!defined('HTTP_CACHE_CONTROL')) define('HTTP_CACHE_CONTROL',    'private');

// 数据库自动连接
if (!defined('DB_AUTO_CONNECT'))    define('DB_AUTO_CONNECT',       false);

if (SHOW_ERROR === true) {
    ini_set('display_errors', 1);
}

require(MINI_PATH . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'Loader.php');
Mini\Loader::getInstance(MINI_PATH);

//一切由此开始
Mini\App::getInstance()->run();
