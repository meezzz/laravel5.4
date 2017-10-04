<?php

/**
 * Created by PhpStorm.
 * User: Myron
 * Date: 2017/9/25
 * Time: 18:58
 */
namespace   App\Http\Controllers\Wechat;
use \DB;
class IndexController
{
    const TOKEN = '20170925wangliuzheng';

    public function index(){
        //获得参数 signature nonce token timestamp echostr
        $nonce     = $_GET['nonce'];
        $token     = self::TOKEN;
        $timestamp = $_GET['timestamp'];
        $echostr   = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );
        if( $str  == $signature && $echostr ){
            //第一次接入weixin api接口的时候
            echo  $echostr;
            exit;
        }else{
            $this->reponseMsg();
        }
    }
    // 接收事件推送并回复
    public function reponseMsg()
    {
        //1.获取到微信推送过来post数据（xml格式）
        $postStr = file_get_contents('php://input');
        //2.处理消息类型，并设置回复类型和内容
        /*<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
</xml>*/
        $postObj = simplexml_load_string($postStr);
        //$postObj->ToUserName = '';
        //$postObj->FromUserName = '';
        //$postObj->CreateTime = '';
        //$postObj->MsgType = '';
        //$postObj->Event = '';
        // gh_e79a177814ed
        //判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            //如果是关注 subscribe 事件
            if (strtolower($postObj->Event == 'subscribe')) {
                //回复用户消息(纯文本格式)
                $toUser = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time = time();
                $msgType = 'text';
                $content = '欢迎关注我们的微信公众账号' . $postObj->FromUserName . '-' . $postObj->ToUserName;
                $template = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>";
                $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $info;
                /*<xml>
                <ToUserName><![CDATA[toUser]]></ToUserName>
                <FromUserName><![CDATA[fromUser]]></FromUserName>
                <CreateTime>12345678</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[你好]]></Content>
                </xml>*/


            }
        }
    }
    
//    //用户发给公众号的消息以及开发者需要的事件推送，将被微信转发到该方法中
//    public function index(){
//        //验证消息的确来自微信服务器
//        $echostr = $_GET['echostr'];
//        $is_from_weixin_server = $this->checkSignature();
//        if($is_from_weixin_server && $echostr){
//            return $echostr;
//        }else{
//            $this->responseMsg();
//        }
////        return view('errors.403');
//    }
//
//    //接收事件推送，并回复
//    public function responseMsg(){
//        /**
//         * <xml>
//        <ToUserName><![CDATA[toUser]]></ToUserName>
//        <FromUserName><![CDATA[FromUser]]></FromUserName>
//        <CreateTime>123456789</CreateTime>
//        <MsgType><![CDATA[event]]></MsgType>
//        <Event><![CDATA[subscribe]]></Event>
//        </xml>
//         */
////        $postStr = file_get_contents('php://input');
//        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
//        $postObj = simplexml_load_string($postStr);
//        if(strtolower($postObj->MsgType) == 'event'){
//            if(strtolower($postObj->Event) == 'subscribe'){
//                //记录关注用户信息（FromUserName），回复用户
//                $toUser = $postObj->FromUserName;
//                $fromUser = $postObj->ToUserName;
//                $createTime = time();
//                $msgType = 'text';
//                $content ='终于等到您！！欢迎关注我们的微信订阅号。';
//
//                $template = "<xml>
//                                <ToUserName><![CDATA[%s]]></ToUserName>
//                                <FromUserName><![CDATA[%s]]></FromUserName>
//                                <CreateTime>%s</CreateTime>
//                                <MsgType><![CDATA[%s]]></MsgType>
//                                <Content><![CDATA[%s]]></Content>
//                            </xml>";
//                $info = sprintf($template,$toUser,$fromUser,$createTime,$msgType,$content);
//                echo $info ;
//            }
//        }
//        /**
//        //消息类型是event，事件
//        if(strtolower($postObj->MsgType) == 'event'){
//            //如果是订阅事件
//            if(strtolower($postObj->Event) == 'subscribe'){
//                //记录订阅用户
//                $data['open_id'] =  $postObj->FromUserName;
//                $data['app_id'] =  $postObj->ToUserName;
//                $data['is_del'] =  1;
//                $data['created_at'] =  date('Y-m-d H:i:s');
//                DB::table('wechat_user_subscribe')->insert($data);
//                //记录关注用户信息（FromUserName），回复用户
//                $toUser = $postObj->FromUserName;
//                $fromUser = $postObj->ToUserName;
//                $createTime = time();
//                $msgType = 'text';
//                $content ='终于等到您！！欢迎关注我们的微信订阅号。';
//
//                $template = "<xml>
//                                <ToUserName><![CDATA[%s]]></ToUserName>
//                                <FromUserName><![CDATA[%s]]></FromUserName>
//                                <CreateTime>%s</CreateTime>
//                                <MsgType><![CDATA[%s]]></MsgType>
//                                <Content><![CDATA[%s]]></Content>
//                            </xml>";
//                $info = sprintf($template,$toUser,$fromUser,$createTime,$msgType,$content);
//                echo $info ;
//            }
//        }
//**/
//    }
//    //验证是否来自微信服务器
//    private function checkSignature()
//    {
//        $signature =  !empty($_GET['signature']) ? $_GET['signature']:'';
//        $timestamp =  !empty($_GET['timestamp']) ? $_GET['timestamp']:'';
//        $nonce =  !empty($_GET['nonce']) ? $_GET['nonce']:'';
//        $token = self::TOKEN;
//        $tmpArr = array($token, $timestamp, $nonce);
//        sort($tmpArr, SORT_STRING);
//        $tmpStr = implode( $tmpArr );
//        $tmpStr = sha1( $tmpStr );
//
//        if( $tmpStr == $signature ){
//            return true;
//        }else{
//            return false;
//        }
//    }

}