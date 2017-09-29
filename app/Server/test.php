<?php
/**
 * Created by PhpStorm.
 * User: Myron
 * Date: 2017/9/29
 * Time: 14:10
 */
require_once './workerman/Autoloader.php';
use Workerman\Worker;
$worker = new Worker('http://0.0.0.0:80');
$worker->onMessage = function ($connection,$data){
    $connection->send('hello http ：'.$data);
};

//运行
Worker::runAll();