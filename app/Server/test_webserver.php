<?php
/**
 * Created by PhpStorm.
 * User: Myron
 * Date: 2017/9/29
 * Time: 15:34
 */
use \Workerman\Worker;
use \Workerman\WebServer;
require_once __DIR__ . '/workerman/Autoloader.php';

// 这里监听8080端口，如果要监听80端口，需要root权限，并且端口没有被其它程序占用
$webserver = new WebServer('http://0.0.0.0:8080');
// 类似nginx配置中的root选项，添加域名与网站根目录的关联，可设置多个域名多个目录
$webserver->addRoot('test.cn', 'E:/git/test');
$webserver->addRoot('laravel5.4.cn', 'E:/git/laravel5.4/public');
// 设置开启多少进程
$webserver->count = 4;

Worker::runAll();