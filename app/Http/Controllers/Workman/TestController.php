<?php

namespace App\Http\Controllers\Workman;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Beanbun\Beanbun;
use Illuminate\Support\Facades\Storage;
class TestController extends Controller
{

    public function test(){
//        $beanbun = new Beanbun;
//        $beanbun->seed = [
//            'http://laravel5.4.cn/admin/user/index'
//        ];//return __DIR__ . '/Info/'.date('Ymd').'/' . md5($beanbun->url);
//        $beanbun->afterDownloadPage = function($beanbun) {
//            file_put_contents(__DIR__ . '/Info/' . md5($beanbun->url), $beanbun->page);
//        };
//        $beanbun->start();

    }

    public function agency(){

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
    }


}
