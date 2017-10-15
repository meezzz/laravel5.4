<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DB;
use \App\Model\Wechat\WechatUserSubscribe;
use   \App\Model\ExceptionLog;
use \App\Model\Weixin\WeixinUserSubscribe;

class TestController extends Controller
{
    //测试方法
    public function test(){
        \DB::connection()->enableQueryLog(); // 开启查询日志
        //记录新增的订阅用户
        $toUser = 'a';
        $fromUser = 'b';
        $open_user_info = WeixinUserSubscribe::where('open_id',$toUser)->first();var_dump($open_user_info);
        $weixin = new WeixinUserSubscribe;
        //如果从未订阅过，则直接记录新用户。
        if(!$open_user_info ){
            $weixin->open_id = $toUser;
            $weixin->server_id = $fromUser;
            $weixin->subscribe = 1;
            $weixin->created_at = date('Y-m-d H:i:s');
            $weixin->updated_at = date('Y-m-d H:i:s');
            $weixin->save();
        }else{
            $weixin->where('open_id',$toUser)->update(['subscribe' => 1]);
        }
        $queries = \DB::getQueryLog();
        dd($queries);
        exit;
    }

}
