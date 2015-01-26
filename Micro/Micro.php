<?php
// +------------------------------------------------------------
// | Micro Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MicroFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

defined('MICRO_PATH')  or define('MICRO_PATH',  __DIR__ . DIRECTORY_SEPARATOR);
defined('APP_PATH') or define('APP_PATH',       dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR);

require(MICRO_PATH .'Library' . DIRECTORY_SEPARATOR . 'Autoloader.php');
Autoloader::getInstance(MICRO_PATH);

//一切由此开始
App::getInstance()->run();
