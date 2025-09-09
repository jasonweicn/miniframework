<?php
namespace App\Console;

use Mini\Console\Action;

/**
 * 这是一个 Console 脚本案例
 * 运行方式：<your_path>/App/run demo/info
 * （运行成功会输出“Hello World!”）
 */
class Demo extends Action
{
    function infoAction()
    {
        // 在 Console 模式中可正常调用 Model
        $info = new \App\Model\Info();
        
        $infoText = $info->getInfo();

        // 输出的内容需要开发者自己处理换行（例如：\n）
        echo $infoText . "\n";

        // 也可以使用框架自带的方法向命令行输出格式化的日志
        //$this->consoleLog($infoText);

        // 返回 0 代表程序正常结束
        return 0;
    }
}
