<?php
/**
 * 应用入口
 */

// 应用命名空间（请与应用所在目录名保持一致）
const APP_NAMESPACE = 'App';

// 定义应用的运行环境，会自动加载带有 -test 后缀的配置文件
const APP_ENV = 'test';

// 是否显示错误信息（默认值：false）
const SHOW_ERROR = true;

// 是否开启日志（生产环境建议关闭，默认值：false）
const LOG_ON = true;

// 是否启用布局功能（默认值：false）
const LAYOUT_ON = true;

// 是否开启REST模式的API接口功能（默认值：false）
const REST_ON = true;

// 是否开启模板功能（默认值：false）
const TPL_ON = true;

// 引入 MiniFramework 就是这么简单
require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'MiniFramework' . DIRECTORY_SEPARATOR . 'Bootstrap.php';
