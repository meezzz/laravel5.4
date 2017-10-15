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
        //记录新增的订阅用户
        $toUser = 'o5SzjwQaPgSjDD9w4DvPgteOjJD0';
        $fromUser = 'gh_62be1c4590a3';
        $open_user_info = WeixinUserSubscribe::where('open_id',$toUser)->get();
        $wexin = new WeixinUserSubscribe;
        //如果从未订阅过，则直接记录新用户。
        if(!$open_user_info){
            $wexin->open_id = $toUser;
            $wexin->server_id = $fromUser;
            $wexin->unsubscribe = 1;
            $wexin->created_at = date('Y-m-d H:i:s');
            $wexin->updated_at = date('Y-m-d H:i:s');
            $wexin->save();
        }else{
            $wexin->where('open_id',$toUser)->update(['subscribe' => 1]);
        }
        $queries = \DB::getQueryLog();
        dd($queries);
        exit;
    }

}
