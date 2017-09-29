<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DB;
use   \App\Model\ExceptionLog;
class TestController extends Controller
{
    //测试方法
    public function test(){
        $wechat = new WechatUserSubscribe;
        $toUser =
        $wechat->where('open_id',$toUser)->update(['unsubscribe' => 1]);
    }

}
