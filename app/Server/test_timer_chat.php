<?php 

require_once './workerman/Autoloader.php';
use Workerman\Worker; 
use Workerman\Lib\Timer;
 
$worker = new Worker('websocket://0.0.0.0:11077'); 
 
$worker->onConnect = function ($connection){ 
    Timer::add(10,function()use($connection){
        if(!isset($connection->name))
            $connection->close('auth name timeout and close');
    },null,false);
}; 
$worker->onMessage = function($connection,$data){
    if(!isset($connection->name)){
        $data = json_decode($data,true);
        if(!isset($data['name']) || !isset($data['password'])){
             return $connection->close('auth fail and close');
        }
        $connection->name = $data['name'];
        return broadcast($connection->send($connection->name ." login"));
    }
    broadcast($connection->name ." said: $data");
};

function broadcast($msg)
{
    global $worker;
    foreach ($worker->connections as $connection) {
        if(!isset($connection->name))
            continue;
	 $connection->send($msg);
    }
}
 
// 运行worker 
Worker::runAll(); 
 





