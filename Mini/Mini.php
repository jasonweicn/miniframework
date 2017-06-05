<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

if (!defined('MINI_PATH'))          define('MINI_PATH',             dirname(__FILE__));
if (!defined('APP_PATH'))           define('APP_PATH',              dirname($_SERVER['SCRIPT_FILENAME']));
if (!defined('SHOW_ERROR'))         define('SHOW_ERROR',            false);
if (!defined('CACHE_PATH'))         define('CACHE_PATH',            APP_PATH . DIRECTORY_SEPARATOR . 'Cache');
if (!defined('CONFIG_PATH'))        define('CONFIG_PATH',           APP_PATH . DIRECTORY_SEPARATOR . 'Config');
if (!defined('LAYOUT_ON'))          define('LAYOUT_ON',             false);
if (!defined('LAYOUT_PATH'))        define('LAYOUT_PATH',           APP_PATH . DIRECTORY_SEPARATOR . 'Layouts');
if (!defined('HTTP_CACHE_CONTROL')) define('HTTP_CACHE_CONTROL',    'private');
if (!defined('DB_AUTO_CONNECT'))    define('DB_AUTO_CONNECT',       false);

require(MINI_PATH . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'Autoloader.php');
Autoloader::getInstance(MINI_PATH);

//一切由此开始
App::getInstance()->run();