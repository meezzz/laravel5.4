<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DB;
use   \App\Model\ExceptionLog;
class TestController extends Controller
{
    //测试方法
    public function test(){
        \DB::connection()->enableQueryLog();
//        $toUser = 'o5SzjwQaPgSjDD9w4DvPgteOjJD0';
        $toUser = 'a';
//        $fromUser = 'gh_62be1c4590a3';
                $fromUser = 'gh_62be1c4590a311_a';

        //记录新增的订阅用户
       $res =  \App\Model\Wechat\WechatUserSubscribe::where('open_id','a')->get();
        $wechat = new \App\Model\Wechat\WechatUserSubscribe;
        if(!$res){
            $wechat->open_id = $toUser;
            $wechat->server_id = $fromUser;
            $wechat->is_del = 0;
            $wechat->created_at = date('Y-m-d H:i:s');
            $wechat->updated_at = date('Y-m-d H:i:s');
            echo 1;
            $res = $wechat->save();
        }else{echo 2;
            $data['server_id'] =$fromUser;
            $res = $wechat->where('open_id',$toUser)->update($data);
        }
        var_dump(\DB::getQuerylog());
       dd($res);
    }

}
