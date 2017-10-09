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
        $appid = \App\Http\Controllers\Wechat\IndexController::APPID;
        $appsecret = \App\Http\Controllers\Wechat\IndexController::APP_SECRET;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return getWechatAccessToken();
    }

}
