<?php
/**
 * 应用入口
 */

// 应用命名空间（请与应用所在目录名保持一致）
const APP_NAMESPACE = 'App';

// 应用路径（通常不需要设置，框架可以识别路径）
//define('APP_PATH',      dirname(dirname(__FILE__)));

// 是否显示错误信息
const SHOW_ERROR = true;

// 是否开启日志（生产环境建议关闭）
const LOG_ON = false;

// 是否启用布局功能
const LAYOUT_ON = true;

// 是否开启REST模式的API接口功能（默认值：false）
//const REST_ON = false;

// 引入 MiniFramework 就是这么简单
//require dirname(APP_PATH) . DIRECTORY_SEPARATOR . 'MiniFramework' . DIRECTORY_SEPARATOR . 'Bootstrap.php';
require __DIR__ . '/../../miniframework/Bootstrap.php';
