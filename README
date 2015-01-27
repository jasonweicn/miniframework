# MicroFramework
===================================
Micro Framework - 一个微小的PHP框架
===================================

一些说明
--------

1.Micro目录中的所有文件为本框架的核心文件。

2.App目录里是一个例子，请将Apache或Nginx的站点根目录指向App中的Public目录。如果你可以通过访问类似于 http://localhost/?c=news&a=main 这样的url地址显示出一个“Hello World!”页面，这说明你已经部署成功了。


关于Rewrite设置
---------------

本框架在设置了Rewrite规则后，可实现下面这种访问方式

http://localhost/news/main/

news - Controller
main - Action

1.运行于 Apache
向Public目录中添加一个.htaccess文件，内容如下：

RewriteEngine on
RewriteRule !.(bmp|gif|ico|jpg|png|js|css)$ index.php

2.运行于 Nginx
在nginx.conf中，找到对应的站点，向server {}中添加如下设置：
location / {
    index  index.html index.php;
    if (!-e $request_filename) {
        rewrite ^/(.*)$ /index.php last;
    }
}