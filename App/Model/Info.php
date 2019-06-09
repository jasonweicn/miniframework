<?php
namespace App\Model;

use Mini\Base\Model;
//use Mini\Base\Config;
//use Mini\Db\Db; // 工厂模式
//use Mini\Db\Mysql; // 直接调用

/**
 * 这是一个模型的案例
 * MiniFramework 从 1.0.0 开始全面启用了命名空间，创建模型时，需在文件顶部放置 namespace App\Model; 进行声明。
 */
class Info extends Model
{
    public function getInfo()
    {
        //如果你开启了数据库自动连接功能，就可以用下面这行代码自动加载数据库对象了
        //$db = $this->loadDb('default');
        
        //这是手工连接数据库的方法，需要解开顶部 use Mini\Db\Db; 和 use Mini\Base\Config; 两行代码的注释
        //$dbParams = Config::getInstance()->load('database:default');
        //$db = Db::factory('Mysql', $dbParams);
        
        //MiniFramework 从 2.0 开始支持直接调用 Mini\Db\Mysql
        //  需要解开顶部的 user Mini\Db\Mysql; 和 use Mini\Base\Config; 两行代码的注释
        //  这样直接调用的好处是可以让IDE更好地对类的方法进行提示，方便开发者进行编码。
        //$dbParams = Config::getInstance()->load('database:default');
        //$db = new Mysql($dbParams);
        
        return "Hello World!";
    }
}
