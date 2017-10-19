<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DB;
use \App\Model\Wechat\WechatUserSubscribe;
use   \App\Model\ExceptionLog;
use \App\Model\Weixin\WeixinUserSubscribe;
use Illuminate\Support\Facades\Redis;


class TestController extends Controller
{
    //测试方法
    public function test(Request $request){
        $weixin = new  \App\Http\Controllers\Weixin\IndexController;
        $res = $weixin->sendMsgAll();
        dd($res);
    }

    public function curltest(){
        session_start();
        $_SESSION['test2'] = 'bb';
        $_SESSION['expire_time'] = time() + 600;
        dd($_SESSION);
        $crontablog= new \App\Model\CrontabLog;
        $crontablog->task_id = 1;
        $crontablog->task_name = 'curl crontab test';
        $crontablog->created_at =date('Y-m-d H:i:s');
        $crontablog->save();
    }

}
