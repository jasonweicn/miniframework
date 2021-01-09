MiniFramework
=============

MiniFramework 是一款遵循 Apache2 开源协议发布的，支持 MVC 和 RESTful 的超轻量级 PHP 开发框架。
MiniFramework 能够帮助开发者用最小的学习成本快速构建 Web 应用，在满足开发者最基础的分层开发、数据库和缓存访问等少量功能基础上，做到尽可能精简，以帮助您的应用基于框架高效运行。

快速入门
--------

MiniFramework 快速入门学习文档：[http://www.miniframework.com/docv2/guide/](http://www.miniframework.com/docv2/guide/)

安装部署
--------

[![Latest Stable Version](https://img.shields.io/packagist/v/jasonweicn/miniframework.svg)](https://packagist.org/packages/jasonweicn/miniframework)
[![Total Downloads](https://img.shields.io/packagist/dt/jasonweicn/miniframework.svg)](https://packagist.org/packages/jasonweicn/miniframework)

通过 Composer 可以快速安装部署一个基于 MiniFramework 的基础应用模板，开发者可以通过这个模板快速开始构建自己的 Web 应用。

### 1.安装 Composer

> 如果已经安装好了 Composer 可跳过本节内容。

在 Linux 系统中，全局安装 Composer 的命令如下：

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

> Windows 和 MacOS 系统的开发者可前往 Composer 的官网 [https://getcomposer.org/](https://getcomposer.org/) 下载对应的安装包进行安装。

### 2.通过 Composer 安装 MiniFramework 基础应用模板

在命令行执行如下命令：

```
composer create-project --prefer-dist --stability=dev jasonweicn/miniframework-app-basic myapp
```

> 上述命令结尾的 myapp 为要创建的项目目录，可根据实际情况修改。

### 3.配置应用

找到 myapp/Public/index.php 文件，这是应用的入口文件，可在其中定义所需的配置常量，例如：

```
<?php
/**
 * 应用入口
 */

// 应用命名空间
const APP_NAMESPACE = 'App';

// 是否显示错误信息
const SHOW_ERROR = false;

// 是否启用布局功能（默认值：false）
const LAYOUT_ON = true;

// 兼容多平台的目录分隔符
const DS = DIRECTORY_SEPARATOR;

// 引入 MiniFramework 就是这么简单
require dirname(__DIR__) . DS . 'vendor' . DS . 'autoload.php';
require dirname(__DIR__) . DS . 'vendor' . DS . 'jasonweicn' . DS . 'miniframework' . DS . 'Bootstrap.php';
```

> 上述代码已经包含在文件中了，最后两行是引入 MiniFramework 框架，通常不需要进行修改即可使用。

### 4.配置站点

请将 myapp/Public 目录配置到 Apache 或 Nginx 作为站点的根目录。

### 5.运行

完成所有配置后，可尝试通过浏览器访问，例如：

http://你的域名/index.php

如页面显示“Hello World!”内容，那么恭喜你，一个基于 MiniFramework 的应用已经运行起来了。


参与开发
--------

欢迎所有人参与到 MiniFramework 的项目中，不论是为 MiniFramework 添加新特性，还是发现了 Bug 进行修正，MiniFramework 向所有人开放！

参与开发的流程：

* 首先，开发者应具有一个 GitHub 账号，在 GitHub 登录账号；
* 进入 MiniFramework 项目页面 [https://github.com/jasonweicn/miniframework](https://github.com/jasonweicn/miniframework)；
* 将 MiniFramework 项目源码 Fork 到开发者自己的账号下，然后 Clone 到本地计算机硬盘中；
* 完成代码编写并 Commit 到开发者账号下的 MiniFramework 副本中；
* 开发者通过 Pull request 提交代码（提交时请详细填写改动细节），等待审核通过。


关于作者
--------

作者：Jason Wei

信箱：jasonwei06@hotmail.com

博客：[http://www.sunbloger.com](http://www.sunbloger.com)

微博：[https://weibo.com/jasonweicn](https://weibo.com/jasonweicn)


开源协议
--------

MiniFramework 遵循 Apache License Version 2.0 开源协议发布。

协议详细内容请浏览项目目录中的 LICENSE 文件。
