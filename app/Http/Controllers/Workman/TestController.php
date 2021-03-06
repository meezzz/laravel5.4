<?php
require_once '../../../vendor/workerman/workerman/Autoloader.php';
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

$worker = new Worker('tcp://0.0.0.0:443');

$worker->onConnect = function ($connection){
    $connection_baidu = new AsyncTcpConnection('tcp://www.baidu.com:443');
    $connection_baidu->onMessage = function ($connection_baidu,$data) use ($connection){
        $connection->send($data);
    };
    $connection->onMessage = function ($connection,$data) use ($connection_baidu){
        $connection_baidu->send($data);
    };
    $connection_baidu->connect();
};

// 运行worker
Worker::runAll();

