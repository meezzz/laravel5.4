<?php

/**
 * Created by PhpStorm.
 * User: Myron
 * Date: 2017/9/25
 * Time: 18:58
 */
namespace   App\Http\Controllers\Wechat;
class IndexController
{
    const TOKEN = '20170925wangliuzheng';
    
    //用户发给公众号的消息以及开发者需要的事件推送，将被微信转发到该方法中
    public function index(){
        //验证消息的确来自微信服务器
        return $this->checkSignature();
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = self::TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

}