<?php
/**
 * 数据库配置-测试环境
 */
return [
    'default' => [
        'host'          => 'localhost', //主机地址
        'port'          => 3306,        //端口
        'dbname'        => 'test',      //库名
        'username'      => 'root',      //用户名
        'passwd'        => '',          //密码
        'charset'       => 'utf8',      //字符编码
        'persistent'    => false        //是否启用持久连接
    ]
];
