<?php

/**
 * 应用入口
 */

//应用路径
define('APP_PATH',      dirname(__FILE__).'/../');

//是否显示错误信息
define('SHOW_ERROR',    true);

//是否启用布局功能
define('LAYOUT_ON',     true);

//引入 MiniFramework 就是这么简单
require '../../Mini/Mini.php';