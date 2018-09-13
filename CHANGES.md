# CHANGES #

## Version 1.4.0 released. ( 2018-09-12 ) ##

* 新增Log类，用于以日志的形式记录代码运行报错和开发者自定义的调试信息。
* 新增常量LOG_ON，用于控制日志功能的开启和关闭（生产环境建议关闭）。
* 新增常量LOG_LEVEL，用于定义可被写入日志的错误等级。
* 新增常量LOG_PATH，用于定义日志存储路径。
* 新增Debug类的varType方法，用于判断变量类型。
* 改进优化异常控制相关功能。

## Version 1.3.0 released. ( 2018-08-29 ) ##

* 新增Debug类，用于程序代码的调试。
* 新增Session类的commit方法，用于提交将当前$_SESSION变量存放的数据。
* 新增Session类的status方法，用于获取当前会话状态。（PHP >= 5.4.0）
* 新增Upload类的setSaveNameLen方法，用于设置上传文件保存时生成的随机文件名长度。
* 新增Upload类的saveOne方法，专门用于上传保存单个文件。
* 改进Upload类的save方法，支持多个文件同时上传保存的新特性。

## Version 1.2.0 released. ( 2018-07-04 ) ##

* 新增Upload类，用于上传文件。
* 新增全局函数getFileExtName()，用于获取文件扩展名。
* 新增全局函数getHash()，用于在分库或分表场景下获取一个指定长度INT型HASH值。
* 新增常量PUBLIC_PATH，用于定义WEB站点跟目录。
* 改进Model类，新增支持连贯操作方式查询数据的特性。

## Version 1.1.1 released. ( 2018-06-19 ) ##

* 修正Registry类命名冲突的bug，将其中的方法unset更名为del。

## Version 1.1.0 released. ( 2018-06-10 ) ##

* 新增Captcha类，用于生成和校验图片验证码
* 新增Registry类的unset方法，用于删除已注册的变量
* 新增全局函数browserDownload()，用于让浏览器下载文件
* 在App目录中，新增名为Example的控制器，其中包含部分功能的示例代码

## Version 1.0.13 released. ( 2018-04-21 ) ##

* 改进Db_Mysql中的execTrans方法
* 改进渲染特性
* 新增全局函数isImage()，用于判断文件是否为图像格式
* 新增全局函数getStringLen()，用于获取字符串长度（支持UTF8编码的汉字）

## Version 1.0.12 released. ( 2017-10-19 ) ##

* 新增Session类，用于读写会话数据

## Version 1.0.11 released. ( 2017-07-16 ) ##

* 改进转换伪静态地址分隔符的机制
* 优化路由处理伪静态时的性能
* 优化部分核心类的属性
* 优化框架内存占用

## Version 1.0.10 released. ( 2017-07-15 ) ##

* 新增支持使用“_”字符作为伪静态URL中的分隔符
* 新增支持伪静态时使用“.html”作为URL结尾的机制

## Version 1.0.9 released. ( 2017-07-10 ) ##

* 修正开启Layout功能同时未调用布局文件时，视图渲染输出时报错的Bug

## Version 1.0.8 released. ( 2017-07-06 ) ##

* 新增在REST模式的API接口中，向Header传入Ver（版本号）实现调用不同版本的API接口的功能
* 新增用于演示API接口版本调用的两个demo文件，分别是Info.php和Info_V2.php

## Version 1.0.7 released. ( 2017-06-29 ) ##

* 新增Cache_Memcached类，用于对PHP的Memcached扩展的支持
* 改进Cache缓存类，增加支持写入永久有效缓存数据的特性，不再强制缓存具有有效期
* 修正Cache缓存类中的验证具体类是否存在的Bug（Close #4）
* 修正Cache_Redis类中del()方法的Bug

## Version 1.0.6 released. ( 2017-06-25 ) ##

* 新增在REST模式的API接口中使用$this->forbidden()方法直接输出403禁止访问的信息
* 新增向REST模式的API接口中不存在的方法发起请求时，自动输出403禁止访问的信息
* 修正Params类中的Bug
* 按PSR-2标准规范代码格式
* 启用支持Markdown的CHANGES.md


## Version 1.0.5 released. ( 2017-06-23 ) ##

* 修正自动连接数据库功能在REST模式的API接口中失效的Bug


## Version 1.0.4 released. ( 2017-06-22 ) ##

* 新增用于获取请求头数据的方法
* 优化部分核心类，减少内存占用


## Version 1.0.3 released. ( 2017-06-20 ) ##

* 改进用于Apache的.htaccess文件
* 优化引导程序中对于常量定义的判断方式
* 其他一些优化工作


## Version 1.0.2 released. ( 2017-06-16 ) ##

* 修正因变更核心类库存放路径导致的Db和Cache类加载报错的Bug (Close #2)


## Version 1.0.1 released. ( 2017-06-16 ) ##

* 更新composer.json


## Version 1.0.0 released. ( 2017-06-16 ) ##

* 新增对命名空间的支持
* 新增对RESTful的支持
* 新增对CLI运行模式的支持
* 新增常量APP_NAMESPACE，用于定义应用的命名空间（默认值为：'App'）
* 新增常量REST_ON，用于控制是否开启REST模式的API接口功能（默认值为：false）
* 新增常量SHOW_DEBUG，用于显示开发者调试输出的信息（默认值为：true）
* 新增可通过Config中的load()方法直接读取配置数组中某个键名对应的内容
* 新增全局函数dump()，用于帮助开发者在程序中输出变量进行调试
* 新增全局函数pushXml()，用于输出XML内容
* 新增全局函数parseDataToXml()，用于转换数据为XML
* 改进路由机制，支持伪静态下路径传参的同时混合问号传参
* 改进异常处理机制，优化异常抛出流程
* 改进自动加载机制，将Autoloader与Loader合并为Loader
* 改进Db类，对是否传入主机、端口等参数进行检查
* 改进Cache类，对使用Memcache或Redis时，是否传入主机和端口参数进行检查
* 改进Params类，以适应更新的PHP版本
* 改进全局函数pushJson()，增加一个用于控制是否直接输出JSON并终止程序的参数
* 改进部分异常报错信息的文字描述
* 改进核心文件命名规范，全局函数库的文件名变更为开头字母大写（Global.func.php）
* 改进应用案例入口文件引入框架的方式，以增强跨平台兼容度
* 修正调用Params类的setParams()方法时出现报错的Bug
* 修正Cache类无法正常连接需要验证密码的Redis主机的Bug
* 变更引导程序文件名，由Mini.php变更为Bootstrap.php，以符合多数开发者的习惯
* 变更控制器命名规则，取消名称后缀“Controller”
* 变更核心类库存放路径，以符合PSR-4规范
* 完善注释
* 启用Apache2开源协议


## Version 0.10.2 released. ( 2017-06-12 ) ##

* 修正调用Params的setParams()方法时出现报错的Bug
* 修正无法正常连接需要验证密码的Redis主机的Bug


## Version 0.10.1 released. ( 2017-06-10 ) ##

* 修正Db模块未对传入的params参数校验的Bug
* 修正Cache模块未对传入的params参数校验的Bug


## Version 0.10.0 released. ( 2017-06-05 ) ##

* 增加对Composer的支持
* 新增Config类，用于读取配置信息
* 新增Config/database.php，用于配置数据库
* 新增Model类，开发者创建模型时，可通过继承Model获得自动创建的数据库对象
* 新增常量HTTP_CACHE_CONTROL，用于设置页面缓存
* 新增常量CONFIG_PATH，用于设置配置文件路径
* 新增常量DB_AUTO_CONNECT，用于设置数据库自动连接（默认值为false）
* 在Loader::loadClass()方法中增加对Class命名规则的校验
* 在setLayout()方法中增加对布局文件命名规则的校验
* 修正Memcache和Redis缓存类中的Bug
* 改进全局函数getRandomString()
* 更新了README
* 完善注释


## Version 0.9.0 released. ( 2017-05-31 ) ##

* 新增Layout类，用于替代原有的布局控制方法。
* 从View中移除getLayout()方法，新的布局控制方法请查阅Layout类。
* 向附带案例App的IndexController.php文件中，增加Models调用方法的示例代码
* 向附带案例App的IndexController.php文件中，增加Layout调用方法的示例代码
* 为保持统一的目录命名风格，将常量CACHE_PATH默认值中的cache变更为Cache
* 新增常量LAYOUT_ON，用于开启或关闭布局控制功能（默认值为false）
* 新增常量LAYOUT_PATH，用于设置布局脚本存放的路径


## Version 0.8.2 released. ( 2017-05-28 ) ##

* 修正全局函数库中base64EncodeImage()方法的一处bug
* 重新调整了CHANGES的格式


## Version 0.8.1 released. ( 2016-08-18 ) ##

* 修正Db_Mysql中的一处bug


## Version 0.8.0 released. ( 2016-08-14 ) ##

* 新增Registry类，用于全局存取变量
* 新增支持Redis的缓存类库
* 新增使用Memcache或Redis缓存类库时，可通过getMemcacheObj()或getRedisObj()获取实例化对象，
  便于使用未封装的方法。
* 在Action类中，增加了存放Request实例的属性，供在动作中调用。
* 在View中新增getLayout()方法，用于在视图中的指定位置调入布局文件
* 在Action中新增_forward()方法
* 在Request中新增setControllerName()和setActionName()方法
* 在Params里新增了getPost和getQuery两个方法
* 向全局函数库中新增了base64EncodeImage()用于将图片转换为base64编码
* 向全局函数库中新增了pushJson()，用于输出JSON并终止程序运行
* 改写了view的渲染方式
* 在Params中的checkInject()里，用preg_match替换ereg
* 针对PHP 5.3.6以前可能存在注入的漏洞进行修补
* Bug fix


## Version 0.7.0 released. ( 2016-01-13 ) ##

* 新增全局函数库
* 新增支持文件存取的缓存类库
* 新增支持Memcache的缓存类库
* 从Cache_Abstract中移除_connect()和close()方法
* 在Cache_Abstract中，将_set()方法更名为set()
* 在Cache_Abstract中，将_get()方法更名为get()
* 在Cache_Abstract中，将_unset()方法更名为del()
* 向全局函数库中新增2个函数chgArrayKey()和getRandomString()


## Version 0.6.0 released. ( 2015-02-08 ) ##

* 框架更名为Mini Framework，缩写依旧为：MF
* 优化完善了Loader中的loadClass方法
* Db_Abstract中新增debug方法
* 新增数据库的异常控制
* Router中新增checkRoute方法
* Bug fix


## Version 0.5.0 released. ( 2015-02-02 ) ##

* Db_Mysql中新增insertAll方法
* Exceptions中新增sendHttpStatus方法
* 新增Request类库
* 框架性能优化


## Version 0.4.0 released. ( 2015-01-30 ) ##

* 新增基于PDO的MySQL的类库


## Version 0.3.0 released. ( 2015-01-28 ) ##

* 新增异常控制机制
* 新增处理GET和POST数据的类库
* Bug fix


## Version 0.2.0 released. ( 2015-01-26 ) ##

* 新增Rewrite路由模式


## Version 0.1.0 released. ( 2015-01-25 ) ##

* The first public version.
