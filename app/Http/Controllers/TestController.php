<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DB;
use \App\Model\Wechat\WechatUserSubscribe;
use   \App\Model\ExceptionLog;
class TestController extends Controller
{
    //测试方法
    public function test(){
       return getWechatServerIp();
    }

}
