# CHANGES #


## Version 2.10.1 released. ( 2024-02-05 ) ##

### 版本变化 ###
* 修复 Mini\Base\Upload 类的 saveOne() 无法正确设置上传文件保存路径的Bug。


## Version 2.10.0 released. ( 2024-02-02 ) ##

### 版本变化 ###
* 新增常量 ERROR_PAGE，默认值为空，用于声明自定义错误页面。
* 新增支持输出自定义错误页的特性。
* 新增自定义错误页的示例代码。
* 新增 Mini\Base\Response 类的 charset() 方法，用于在响应头中自定义字符编码。
* 新增 Mini\Base\Rest 类的 response() 和 type() 方法，对输出进行统一封装。
* 调整错误信息输出方式，当启用 REST 模式对 API 接口请求遇到异常时，将以 JSON 格式输出错误信息。
* 改进 Mini\Base\App 类的 dispatch() 方法，支持传入参数带入 Action 中。
* 改进 Mini\Base\App 类，增加名为 isApi 属性，用于判断当前请求是否为 REST 接口。
* 改进 Mini\Base\Action 类的 forward() 方法，支持跳转时传递参数。
* 改进 Mini\Base\Rest 类，在构造阶段即将默认的 json 方式传递给 Response 对象。
* 改进 Mini\Base\Exception 类，在 CLI 模式下运行时默认输出错误信息。
* 改进 Mini\Base\Loader 类，在自动加载过程遇到文件不存在时不主动抛出错误。
* 改进 Mini\Base\Layout 类的 setLayout() 方法，参数允许留空或传入 null 以清除历史布局设置。
* 调整 Mini\Base\Layout 类，取消单例模式，改为常规的实例化对象方式。
* 改进 Mini\Base\Action 类的 forward() 方法，跳转前默认清除历史的布局设置。
* 改进 Mini\Base\View 类的属性声明方式，以兼容 PHP 7.2 和 7.3 版本。
* 改进框架默认的报错输出格式，优化阅读体验。
* 修复 Mini\Cache\File 类的 set() 和 del() 两个方法中写入和删除文件的Bug。
* 修复配置自定义路由与 CLI 模式运行时出现的路由冲突问题。


## Version 2.9.9 released. ( 2024-01-09 ) ##

### 版本变化 ###
* 新增支持在控制中通过名为 _end 的方法名，在所有动作结束后执行一些代码。
* 修复在 PHP 8.2 中模板渲染的Bug。
* 改进 autoload 自动装载在特定环境下的兼容性（感谢网友繁星点点反馈此问题）。


## Version 2.9.8 released. ( 2024-01-07 ) ##

### 版本变化 ###
* 将 Mini\Base\Debug 迁移至 Mini\Helpers\Debug 后续作为助手类使用。
* 新增 Mini\Helpers\Debug 类的 memoryUsage() 方法，用于获取内存占用情况。
* 调整代码对 PHP 8.2 以上版本进行兼容。


## Version 2.9.7 released. ( 2022-12-26 ) ##

### 版本变化 ###
* 改进 Mini\Base\Model 类的 add() 方法，调整参数为布尔类型，用于开闭预处理。
* 改进 Mini\Base\Model 类的 save() 方法，支持使用 debug() 方法时输出 SQL 语句。
* 改进 Mini\Base\Model 类的 delete() 方法，支持使用 debug() 方法时输出 SQL 语句。
* 改进 Mini\Db\Mysql 类，使用预处理插入数据时，支持 debug 输出 SQL 语句。
* 改进 Mini\Base\Model 类，增加事务处理的相关方法。


## Version 2.9.6 released. ( 2022-12-19 ) ##

### 版本变化 ###
* 新增常量 LOG_MAX_FILES，默认值30，用于定义日志文件存储的最大数量。
* 新增 Mini\Base\Model 类的 distinct() 方法，用于链式操作支持 SQL 的 DISTINCT 语句。
* 新增 Mini\Base\Model 类的 having() 方法，用于链式操作支持 SQL 的 HAVING 语句。
* 新增 Mini\Base\Model 类的 page() 方法，帮助开发者更简单地实现分页功能。
* 改进 Mini\Base\Model 类的 select() 方法，对于传入参数的字符大小写不再做限制。
* 改进 Mini\Base\Log 类的 writeToFile()方法，根据常量 LOG_MAX_FILES 定义的数量控制日志文件个数。
* 改进 Mini\Db\Mysql 类的 query() 方法，对于传入参数的字符大小写不再做限制。


## Version 2.9.5 released. ( 2022-12-06 ) ##

### 版本变化 ###
* 新增 Mini\Base\Request 类的 getHost() 方法，用于获取当前请求的 HOST 地址。
* 新增 Mini\Base\Request 类的 getUserAgent() 方法，用于获取当前请求的客户端 User-Agent 信息。
* 改进 Mini\Base\Request 类的 getHeaders() 方法，支持获取指定名称的 Header 信息。
* 改进 Mini\Base\Model 类的 regDb() 方法，当注册的数据库对象已经存在时会抛出异常。
* 改进 Mini\Base\Model 类的 useDb() 方法，当无法正常使用数据库对象时会抛出异常。
* 改进 Mini\Base\Layout 类的 setLayoutPath() 方法，增加针对参数的类型约束。
* 修复 Mini\Db\Mysql 类在某些特定场景下无法正常加载 PDO 扩展的问题。
* 优化 Mini\Db\Mysql 类的异常报错信息。


## Version 2.9.4 released. ( 2022-11-28 ) ##

### 版本变化 ###
* 改进 Mini\Base\Model 类的 where() 方法，对传入的数学和逻辑运算符号大小写不敏感。
* 改进 Mini\Db\Mysql 类中抛出异常的方式。
* 修复日志在特定场景下会重复记录的Bug。
* 优化异常信息显示和日志记录格式。
* 优化异常处理机制，将与异常处理相关的方法迁移至 Mini\Base\Exception 类。
* 完善找不到数据库对象时的异常报错信息。


## Version 2.9.3 released. ( 2022-11-21 ) ##

### 版本变化 ###
* 改进全局函数 getClientIp()，完善对客户端 IP 地址获取的准确性。
* 改进 Mini\Security\Sign 类，支持对原始输入流进行签名校验。
* 改进 Mini\Base\Config 类的 load() 方法，支持更灵活的配置文件格式。
* 改进 Mini\Base\Router 类，新增 customRoute() 方法，用于自定义路由规则。


## Version 2.9.2 released. ( 2022-11-11 ) ##

### 版本变化 ###
* 新增 Mini\Base\Request 类的 getInputStream() 方法，用于获取原始输入流。
* 改进 Mini\Base\Router 类，将主要的路由代码迁移到一个新的 route() 方法中，为后续扩展做准备。
* 改进 Mini\Base\Router 的 setRouteType() 方法，增加针对 $type 的类型验证。
* 改进 Mini\Base\View 的 render() 方法，当模板缓存无法写入时抛出异常。


## Version 2.9.1 released. ( 2022-11-06 ) ##

### 版本变化 ###
* 新增 Mini\Base\Action 类的 redirect() 方法，用于进行显性跳转。
* 新增 Mini\Base\Session 类的 has() 方法，替代原有 is_set() 方法。
* 改进 Mini\Base\View 类的 render() 方法，当 Cache 目录不存在时尝试自动创建。
* 改进 Mini\Security\Sign 类，未定义签名加盐时，默认以 APP_PATH 作为盐进行签名加密。
* 变更 Mini\Security\Sign 类的 $expireTime 签名过期时间属性默认值，从300秒缩短为10秒。
* 修复 Mini\Base\Action 的 forward() 方法转向后，原调用方法会继续执行的 Bug。


## Version 2.9.0 released. ( 2022-10-28 ) ##

### 版本变化 ###
* 新增 Mini\Base\Header 类，用于处理 Request 和 Response 的 Header 信息。
* 新增 Mini\Base\Response 类，用于响应客户端，控制请求结果的输出。
* 新增 Mini\Base\App::setAction() 方法，用于设置动作。
* 新增 Mini\Base\App::setController() 方法，用于设置控制器。
* 新增 Mini\Base\Action::forward() 替代原 _forward() 方法，旧方法暂时保留，新旧两个方法功能完全一致。
* 新增 Mini\Security\Sign 类的 setEncryptType() 方法，用于指定加密方式。
* 改进 Controller 和 Action 的设置由 Mini\Base\App 类负责处理。
* 改进在部分核心类库中用 Mini\Base\Response 替代 Mini\Base\Http 以规范响应输出。
* 改进并优化框架异常报错的特性。
* 修复 Action 中使用 $this->_forward() 跳转相同的 Action 时出现死循环的 Bug。
* 修复 Mini\Base\Http 在被继承的场景中可能出现的实例获取Bug。
* 修复全局函数 isTimestamp() 校验时间戳的 Bug。

### 升级说明 ###
* 兼容 PHP 最低版本为 7.2.0，PHP 8.0.0 已测试可正常运行。
* 当前版本向前兼容至 2.4.0 版本，使用 2.4.0 及后续版本的开发者可直接升级至 2.9.0 版本。
* 文档已同步更新，地址：[http://www.miniframework.com/docv2/guide/](http://www.miniframework.com/docv2/guide/)


## Version 2.8.6 released. ( 2022-10-20 ) ##

### 版本变化 ###
* 新增 getRemoteFileSize() 全局函数，用于获取远程文件大小。
* 新增 Mini\Base\Request::getMethod() 替代原 method() 方法，旧方法暂时保留，新旧两个方法功能完全一致。
* 新增 Mini\Base\App::beforeShutdown() 方法，用于在程序终止前处理异常报错等事项。
* 改进 Mini\Base\Http::response() 方法中遇到异常的报错方式。
* 改进 Mini\Base\Http 类的单例实例化方式，实现可被继承的特性。


## Version 2.8.5 released. ( 2022-07-29 ) ##

### 版本变化 ###
* 格式化 Header 信息输出。
* 改进 getClientIp() 全局函数，兼容 IPv6 地址格式。
* 改进校验客户端 CSRF Token 无效时的报错方式。


## Version 2.8.4 released. ( 2022-03-30 ) ##

### 版本变化 ###
* 改进 Mini\Base\Session 类，允许 get 方法在不传入参数时返回全部的会话数据。
* 改进 Mini\Base\Request 类的 getHeader 方法，当指定的名称不存在时返回 null，不指定名称时返回全部 header 数据。


## Version 2.8.3 released. ( 2022-03-11 ) ##

### 版本变化 ###
* 修复 Mini\Db\Mysql 类中执行事务时的Bug。


## Version 2.8.2 released. ( 2022-01-27 ) ##

### 版本变化 ###
* 新增模板引擎自动渲染 Layout 文件的特性。
* 新增模板引擎通过 {const:常量名} 的标记识别输出常量。
* 新增模板引擎兼容 {endBlock} 和 {endBlock:name} 两种代码块结束标记。
* 修复模板引擎无法正确识别数组的Bug。
* 修复模板引擎缓存文件在配置不当时可能通过浏览器访问源代码的问题。


## Version 2.8.1 released. ( 2022-01-26 ) ##

### 版本变化 ###
* 修复模板引擎关闭状态下页面无法正常渲染的Bug。
* 修复在某些特殊情况下模板缓存无法被更新的问题。


## Version 2.8.0 released. ( 2022-01-25 ) ##

### 版本变化 ###
* 新增常量 TPL_ON，默认值为 false，用于定义是否开启模板引擎。
* 新增常量 TPL_SEPARATOR_L，默认值为"{"，用于定义模板标记开始符号。
* 新增常量 TPL_SEPARATOR_R，默认值为"}"，用于定义模板标记结束符号。
* 新增模板引擎，支持在 View 和 Layout 中使用类似 {$info} 的标记输出变量。
* 改进 Mini\Base\Model::select() 方法，允许传入参数 "One"，等价于 "Row"，贴近多数人的习惯。
* 修复 Layout 在某些特殊场景无法正确通过 isset 判断布局是否已经设置的问题。

### 升级说明 ###
* 兼容 PHP 最低版本为 7.2.0，PHP 8.0.0 已测试可正常运行。
* 当前版本向前兼容至 V2.4.0 版本，使用 V2.4.0 及后续版本的开发者可直接升级至 V2.8.0 版本。
* 文档已同步更新，地址：[http://www.miniframework.com/docv2/guide/](http://www.miniframework.com/docv2/guide/)


## Version 2.7.0 released. ( 2021-09-19 ) ##

### 版本变化 ###
* 新增常量 APP_ENV，默认值为"prod"，用于定义应用运行环境。
* 新增应用运行环境支持，可根据常量 APP_ENV 定义的环境加载对应的配置文件。
* 新增 join、innerjoin、leftjoin 和 rightjoin 连贯操作方法，用于联表查询。
* 新增 from 连贯操作方法，用于定义查询的数据表名（为符合使用习惯，封装了原 table 方法）。
* 新增通过 from 或 table 方法传入数组类型参数，对表名和别名进行定义的特性。
* 新增 debug 连贯操作方法，用于在执行数据库查询命令前输出显示最终拼装的 SQL 语句。
* 改进部分单例类，将 __clone 改为私有方法，防止由克隆引起的异常。
* 改进 Mini\Base\Model 类，取消自动追加"`"符号的特性，以增加兼容性。
* 修复 Mini\Base\Model::field() 方法遇到传入"*"时处理保留字的 Bug。

### 升级说明 ###
* 兼容 PHP 最低版本为 7.2.0，PHP 8.0.0 已测试可正常运行。
* 当前版本向前兼容至 V2.4.0 版本，使用 V2.4.0 及后续版本的开发者可直接升级至 V2.7.0 版本。
* 文档已同步更新，地址：[http://www.miniframework.com/docv2/guide/](http://www.miniframework.com/docv2/guide/)


## Version 2.3.4, 2.4.2, 2.5.1, 2.6.1 released. ( 2021-01-12 ) ##

### 版本变化 ###
* 修复：框架在 MVC 模式运行时，常量 SHOW_DEBUG 失效的 Bug。


## Version 2.6.0 released. ( 2021-01-10 ) ##

### 版本变化 ###
* 新特性：新增常量 URL_SUFFIX，默认值为"htm|html|shtml"，用于定义伪静态扩展名。
* 新特性：新增常量 URL_SPLIT_SYMBOL，默认值为"_"，用于定义伪静态时 URL 参数的分割符号。
* 新特性：新增常量 LOG_MODE，默认值为1，用于定义日志存储模式（1为文件，2为数据库）。
* 新特性：新增常量 LOG_DB_CONFIG，默认值为"database:default"，用于定义日志存储的数据库配置（LOG_MODE为2时生效）。
* 新特性：新增常量 LOG_TABLE_NAME，默认值为"log"，用于定义日志存储的数据表名（LOG_MODE为2时生效）。
* 新特性：新增 Mini\Db\Mysql::checkTableIsExist() 方法，用于检查数据表是否存在。
* 新特性：改进 Mini\Base\Log 类，支持日志存入数据库。
* 新特性：改进 Mini\Base\Request 类，支持识别通过常量 URL_SUFFIX 定义的伪静态扩展名。
* 新特性：支持按自定义的伪静态 URL 参数分割符号进行访问请求解析的新特性。
* 新特性：改进 arrayToUrlParams() 全局函数，新增按伪静态模式拼接参数的方式。

### 升级说明 ###
* 兼容 PHP 最低版本为 7.2.0，PHP 8.0.0 已测试可正常运行。
* 当前版本向前兼容至 V2.4.0 版本，使用 V2.4.0 及后续版本的开发者可直接升级至 V2.6.0 版本。
* 文档已同步更新，地址：[http://www.miniframework.com/docv2/guide/](http://www.miniframework.com/docv2/guide/)


## Version 2.5.0 released. ( 2021-01-01 ) ##

### 版本变化 ###
* 新增全局函数 arrayToUrlParams()，用于将数组转换为请求参数(例如：a=1&b=2&c=3)
* 新增 Mini\Security\Sign::setSalt() 方法，用于设置一个自定义的 Salt 字符串。
* 新增 Mini\Base\Model::getLastSql() 方法，用于在模型中获取最近一条被执行的SQL语句，便于开发者进行调试。
* 改进 Mini\Base\Model::where() 方法，支持全新的查询条件设置方法（原有自定义字符串方式依旧支持）。
* 改进 Mini\Db\Mysql::insertAll() 方法，增加参数 $prepare，默认值为 TRUE，用于确定是否进行预处理。
* 改进 Mini\Db\Mysql::insert() 方法，增加参数 $prepare，默认值为 TRUE，用于确定是否进行预处理。
* 改进 Mini\Base\Model::add() 方法，改为通过预处理方式插入数据。
* 改进 Mini\Db\Mysql::prepareInsertAll() 方法，完善对于传入数据格式的校验。
* 改进 browserDownload() 全局函数，读取下载文件后终止脚本运行，避免浏览器下载无用数据。
* 测试支持 PHP 8.0。
* 统一代码注释风格。

### 升级说明 ###
* 兼容 PHP 最低版本为 7.2.0，PHP 8.0.0 已测试可正常运行。
* 当前版本向前兼容至 V2.4.0 版本，使用 V2.4.0 及后续版本的开发者可直接升级至 V2.5.0 版本。
* 文档已同步更新，地址：[http://www.miniframework.com/docv2/guide/](http://www.miniframework.com/docv2/guide/)


## Version 2.4.1 released. ( 2020-12-26 ) ##

* 修复 Mini\Cache\Memcached 因类名错误导致的无法正常实例化的Bug。
* 修复 Mini\Cache\Redis 因类名错误导致的无法正常实例化的Bug。
* 修复 Mini\Cache\Redis::_connect() 方法中无法正常创建连接的Bug。
* 感谢 freshwind2004 反馈上述Bug。


## Version 2.4.0 released. ( 2020-07-11 ) ##

**版本变化**
* 新增 isTimestamp() 全局函数，用于判断一个字符串是否为 UNIX 时间戳格式。
* 新增 CSRF_TYPE 常量，默认值为cookie，用于定义客户端获取 CSRF 令牌的方式。
* 新增 header 方式获取 CSRF 令牌的特性，默认 X-Mini-Csrf-Token 为消息头名称。
* 新增 Mini\Security\Sign 类，用于对 GET 或 POST 数据进行签名和校验。
* 在示例 Example 控制器中，增加了通过 Mini\Security\Sign 类进行签名和校验的示例代码。
* 为提升运行效率将 CSRF 令牌校验改为默认禁用状态，因此变更常量 CSRF_TOKEN_ON 默认为 FALSE。
* 改进视图渲染和输出方式，统一由 Mini\Base\Http 负责最终输出。
* 基于安全考虑，从响应头中删除 MiniFramework 文字信息。

**升级说明**
* 兼容 PHP 最低版本为 7.2.0。
* 当前版本常量 CSRF_TOKEN_ON 默认值有变化，向前不兼容，升级前请确认并进行测试。
* 文档已同步更新，地址：[http://www.miniframework.com/docv2/guide/](http://www.miniframework.com/docv2/guide/)


## Version 2.3.3 released. ( 2020-07-05 ) ##

* 修复REST模式运行时的一个Bug，感谢PndOS反馈此Bug。


## Version 2.3.2 released. ( 2020-06-17 ) ##

* 修复通过PHP内置WEB服务器运行框架时出现NOTICE报错的Bug，感谢codetyphon反馈此Bug。


## Version 2.3.1 released. ( 2020-02-29 ) ##

* 修复Mini\Base\Upload::saveOne()方法中无法正常抛出异常的Bug
* 完善部分代码中遇到错误的异常提示
* 完善示例应用兼容多平台目录分隔符


## Version 2.3.0 released. ( 2020-02-19 ) ##

* 新增方法Mini\Db\Mysql::prepareInsert()，用于按预处理方式向MySQL插入记录
* 新增方法Mini\Db\Mysql::prepareInsertAll()，用于按预处理方式向MySQL批量插入记录
* 在示例应用入口index.php中，改用const声明常量，优化框架性能
* 在部分文件中，启用PHP7支持的批量引入命名空间的代码写法
* 全面启用方括号方式定义数组
* 更新composer.json定义，放弃对于PHP5的兼容性支持，提升PHP最低版本要求至7.2.0


## Version 2.2.0 released. ( 2019-10-30 ) ##

* 新增Block（代码块）机制，用于在视图中的任意位置定义或输出Block数据
* 新增在视图中通过$this->setJsFile()方法设置JS文件资源在body标签前加载的特性
* 改进模型中数据库的连贯操作特性，允许field()方法传入数组形式来指定字段名
* 修正Session::destroy()方法在某些环境中无法正常销毁会话数据的Bug


## Version 2.1.0 released. ( 2019-10-17 ) ##

* 新增全局函数 isIndexArray()，用于判断一个数组是否为索引数组。
* 改进 Mini\Base\Model 类，为在模型中的数据库连贯操作增加若干新特性。
* 完善代码注释。


## Version 2.0.1 released. ( 2019-06-13 ) ##

* 修复创建CSRF-Token的cookie时，路径参数path不固定，导致后续校验失败的Bug。


## Version 2.0.0 released. ( 2019-06-11 ) ##

* 重构框架核心架构，按功能模块划分目录和命名空间。
* 新增命名空间Mini\Base，用于框架基础类库。
* 新增命名空间Mini\Cache，用于缓存类库。
* 新增命名空间Mini\Captcha，用于验证码等人机识别校验类库。
* 新增命名空间Mini\Db，用于数据库操作类库。
* 新增命名空间Mini\Helpers，用于全静态助手类库。
* 新增常量CSRF_TOKEN_ON，默认值为TRUE，用于控制防御CSRF跨站请求伪造攻击功能的开启和关闭。
* 新增方法Mini\Base\Request::checkCsrfToken()，用于校验客户端传入CSRF-Token。
* 新增方法Mini\Base\Request::createCsrfToken()，用于生成一个新的CSRF-Token。
* 新增方法Mini\Base\Request::getCsrfParamName()，用于获取CSRF-Token存储键名。
* 新增方法Mini\Base\Request::loadCsrfToken()，用于读取CSRF-Token。
* 新增方法Mini\Helpers\Safe::getCsrfToken()，用于随时获取当前存储于Server端的CSRF-Token。
* 改进Mini\Db类库，支持原有工厂模式调用和直接调用MySQL类Mini\Db\Mysql两种模式并存。
* 改进Mini\Cache类库，支持原有工厂模式和直接调用File、Memcache、Memcached和Redis类两种模式并存。
* 创建框架核心代码仓库 https://github.com/jasonweicn/miniframework-core 用于正式版本发布。
* 完善用于演示的应用示例App。
* 完善Composer配置，更好的支持在项目中通过Composer引入框架进行编码。
* 完善代码注释。


## Version 1.5.2 released. ( 2019-06-06 ) ##

* 新增全局函数htmlEncode()，用于转换特殊字符为HTML实体字符，便于防范XSS攻击。
* 更新composer.json中定义的包名，从命名上与Github的仓库名称保持一致。


## Version 1.5.1 released. ( 2018-11-16 ) ##

* 修正App类showError方法中的一处bug，感谢Zeng Xi反馈此bug。


## Version 1.5.0 released. ( 2018-11-14 ) ##

* 新增Loader类的loadFunc方法，用于加载开发者自定义的扩展函数。
* 新增开发者自定义扩展函数的示例文件。
* 改进应用启动时加载全局函数的方法。
* 改进Params类，完善setParam和setParams方法对传入参数的校验。
* 针对输出JSON格式进行安全性更新。
* 清理废弃代码。


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
