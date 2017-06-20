MiniFramework 简介
====================

MiniFramework 是一款遵循 Apache2 开源协议发布的，支持 MVC 和 RESTful 的超轻量级 PHP 开发框架。
MiniFramework 能够帮助开发者用最小的学习成本快速构建 Web 应用，在满足开发者最基础的分层开发、数据库和缓存访问等少量功能基础上，做到尽可能精简，以帮助您的应用基于框架高效运行。

目录结构
====================

```
MiniFramework/
|--- App/                   应用案例
|    |--- Api/              REST模式的API
|    |--- Cache/            缓存
|    |--- Config/           配置
|    |    |--- database.php 数据库配置文件
|    |
|    |--- Controller/       控制器
|    |--- Layout/           布局
|    |--- Model/            模型
|    |--- Public/           站点根目录
|    |    |--- css/         css
|    |    |--- img/         图片
|    |    |--- js/          js
|    |    |--- index.php    应用入口文件
|    |
|    |--- View/             视图
|
|--- MiniFramework/         框架核心目录
     |---Functions/         函数库
     |---Library/           类库
     |---Bootstrap.php      引导程序
```

部署应用
====================

请将 Apache 或 Nginx 的站点根目录指向 App 中的 Public 目录。

如果你可以通过访问类似于

`http://localhost/index.php?c=index&a=index`

这样的 URL 获得一个“Hello World!”页面，这说明你已经部署成功了。

应用入口
====================

使用 MVC 开发模式时，通常需要为应用准备一个入口文件，所有对应用的访问请求都应指向这个入口文件，MiniFramework 也不例外。

在附带的应用案例中，找到 `App/Public/index.php`，这就是一个入口文件，其代码如下：

```
//应用命名空间（请与应用所在目录名保持一致）
define('APP_NAMESPACE', 'App');

//应用路径
define('APP_PATH',      dirname(dirname(__FILE__)));

//是否显示错误信息
define('SHOW_ERROR',    true);

//是否启用布局功能
define('LAYOUT_ON',     true);

//是否开启REST模式的API接口功能（默认值：false）
define('REST_ON',       true);

//引入 MiniFramework 就是这么简单
require dirname(APP_PATH) . DIRECTORY_SEPARATOR . 'MiniFramework' . DIRECTORY_SEPARATOR . 'Bootstrap.php';
```
在上边的代码中，最为关键的是最后一行，通过 `require` 命令引入 MiniFramework 的引导程序 `Bootstrap.php`。

在引入引导程序前，你还可以像案例中一样，通过 `define` 命令定义一些 MiniFramework 的关键常量，例如用于显示报错信息的常量 `SHOW_ERROR` 。

提示：MiniFramework 运行所需的全部常量可以在引导程序 `Bootstrap.php` 中找到（1.0.0 版之前引导程序名为 Mini.php）。

设置伪静态
====================

本框架在设置了 Rewrite 规则后，可实现类似下面这种伪静态访问方式

`http://localhost/Controller/Action/param1/value1/param2/value2`

运行于 Apache 的设置方法：

向 Public 目录中添加一个 .htaccess 文件（附带的应用案例中已提供），内容如下：

```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

运行于 Nginx 的设置方法：

在 nginx.conf 中，找到对应的站点，向 server{} 中添加如下设置：

```
location / {
    index  index.html index.php;
    if (!-e $request_filename) {
        rewrite ^/(.*)$ /index.php last;
    }
}
```

命名规则
====================

控制器：只允许使用`a-z`、`A-Z`、`0-9`和`_`，并以大写字母开头，例如：`Index.php`

模型：只允许使用`a-z`、`A-Z`、`0-9`和`_`，并以大写字母开头，例如：`Info.php`

布局：只允许使用`a-z`、`A-Z`、`0-9`和`_`，并以字母开头，例如：`header.php`

命名空间
====================

从 1.0.0 版开始，MiniFramework 已全面启用命名空间，其中：

`Mini` 对应的框架核心

`App` 对应你的应用，可以通过定义常量 `APP_NAMESPACE` 的值来改变应用的命名空间，例如：

```
define('APP_NAMESPACE', 'MyApp'); //建议与应用目录名保持一致
```

创建控制器时，请在页面顶部放置用于声明命名空间的代码，例如：

```
namespace App\Controller; //声明当前页的命名空间

use Mini\Action; //引入Action，因为Action是框架核心文件，所以前面要加Mini\

class Index extends Action
{
    function indexAction()
    {
        //...
    }
}
```

创建模型时，同样要在页面顶部放置用于声明命名空间的代码，例如：

```
namespace App\Model; //声明当前页的命名空间为 App\Model

use Mini\Model; //引入框架核心文件

class Info extends Model
{
    public function getInfo()
    {
        //...
    }
}
```

开启 REST 模式的 API 接口功能
====================

MiniFramework 从 1.0.0 版开始，增加了对 RESTful 的支持，可以在入口文件 `Public/index.php` 中，定义常量 `REST_ON` 的值为 `true` 开启 REST 模式的 API 接口功能，例如：

```
define('REST_ON', true);
```

开启 REST 后，可以访问应用案例中附带的一个名为 `Version` 的接口 demo 进行测试

文件位于 `App/Api/Version.php`，访问方式为：`http://你的域名/api/version`

访问后，正常情况下会得到如下输出结果：

```
{"code":200,"msg":"success","data":"1.0.0"}
```

需要特别注意的是：如果你的项目中有使用 `Api` 命名的 Controller，将会因 REST 开启而失效，所有向 `Api` 的请求均会被指向 `App/Api` 目录。

提示：MiniFramework 的 REST 接口支持输出 `JSON` 和 `XML` 两种数据格式，附带的 demo 中已经进行了演示。

连接数据库
====================

MiniFramework 目前只支持 MySQL 数据库，有自动和手动两种连接方式。

手动连接方法：

```
//如果未在页面顶部用 use 引入 Db，按照下面的写法，在 Db 前加上 \Mini\
$db = \Mini\Db::factory ('Mysql',
    array (
        'host'          => 'localhost', //主机地址
        'port'          => 3306,        //端口
        'dbname'        => 'mydbname',  //库名
        'username'      => 'myuser',    //用户名
        'passwd'        => '123456',    //密码
        'charset'       => 'utf8',      //字符编码
        'persistent'    => false        //是否启用持久连接 （ true | false ）
    )
);

//还可以通过 Config 中的 load() 方法先读取数据库配置，再创建对象
$dbConfig = \Mini\Config::getInstance()->load('database');
$db2 = Db::factory ('Mysql', $dbConfig['default']);
```

提示：`Config::getInstance()->load('database')` 这个方法还可以传入 `database:default` 来直接获取 `default` 中的数据（从 1.0.0 开始支持）

自动连接方法：

MiniFramework 的自动连接数据库功能默认是关闭的，如需使用，请在你的应用入口文件 `Public/index.php` 中定义常量 `DB_AUTO_CONNECT` 的值为 `true`，例如：

```
define('DB_AUTO_CONNECT', true);
```

同时，还需要在 `Config/database.php` 中对数据库连接进行配置，例如：

```
$database['default'] = array (
    'host'          => 'localhost', //主机地址
    'port'          => 3306,        //端口
    'dbname'        => 'test',      //库名
    'username'      => 'root',      //用户名
    'passwd'        => '',          //密码
    'charset'       => 'utf8',      //字符编码
    'persistent'    => false        //是否启用持久连接 （ true | false ）
);
```

接下来就可以在模型中通过 `$this->loadDb()` 方法直接加载数据库对象了，例如：

```
namespace App\Model;

use Mini\Model;

class Info extends Model //自动连接数据库，必须继承核心类 Model
{
    public function getInfo()
    {
        //加载 key 为 default 的数据库
        $db = $this->loadDb('default');
        ...
    }
}
```

使用缓存
====================

MiniFramework 支持三种缓存方式，分别是：Memcache、Redis 和 File（磁盘文件存储）。

使用方法如下：

```
//以最常用的 Memcache 为例
$cache = \Mini\Cache::factory ('Memcache',
    array (
        'host'      => 'localhost', //主机
        'port'      => 11211,       //端口
        'prefix'    => 'MINI_'      //缓存名前缀，默认值为空
    )
);

//写入一个名为 test 的缓存，值为 abc，有效时间为 3600 秒
$cache->set('test', 'abc', 3600);

//读取名为 test 的缓存
$test = $cache->get('test');
```

使用布局
====================

MiniFramework 的布局 (Layout) 功能默认是关闭的，如需使用，请在你的应用入口文件 `Public/index.php` 中定义常量 `LAYOUT_ON` 的值为 `true`，例如：

```
define('LAYOUT_ON', true);
```

提示：附带的应用案例中，已经开启了布局功能，并演示了如何使用布局。

全局函数
====================

MiniFramework 在初始化时，会自动加载一个全局函数库，你可以随时调用全局函数，例如：

```
$test = array('a', 'b', 'c');

//调用全局函数 pushJson() 输出一个 JSON 串并终止程序运行
pushJson($test);
```

提示：全局函数库位于 `Mini/Functions/Global.func.php`

其他
====================

关于控制器、模型和视图的使用方法，请参考附带的应用案例中提供的相关代码。

关于作者
====================

作者：Jason Wei

信箱：jasonwei06@hotmail.com

博客：http://www.sunbloger.com

开源协议
====================

MiniFramework 遵循 `Apache License Version 2.0` 开源协议发布。

协议详细内容请浏览项目目录中的 LICENSE 文件。
