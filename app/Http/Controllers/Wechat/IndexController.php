<?php

/**
 * Created by PhpStorm.
 * User: Myron
 * Date: 2017/9/25
 * Time: 18:58
 */
namespace   App\Http\Controllers\Wechat;
use \DB;
use \App\Model\Wechat\WechatUserSubscribe;
class IndexController
{
    const TOKEN = '20170925wangliuzheng';

    //用户发给公众号的消息以及开发者需要的事件推送，将被微信转发到该方法中
    public function index(){
        exception_log('index','wechat_index');
        //验证消息的确来自微信服务器
        $echostr = isset($_GET['echostr']) ? $_GET['echostr'] : '';
        $is_from_wechat_server = $this->checkSignature();
        exception_log('echostr：'.$echostr.'--is_from_wechat:'.$is_from_wechat_server,'wechat_mp_conf');
        if($is_from_wechat_server && $echostr){
            return $echostr;
        }else{
            $this->responseMsg();
        }
        return view('errors.403');
    }

    //接收事件推送，并回复
    public function responseMsg(){
        /**
         * <xml>
        <ToUserName><![CDATA[toUser]]></ToUserName>
        <FromUserName><![CDATA[FromUser]]></FromUserName>
        <CreateTime>123456789</CreateTime>
        <MsgType><![CDATA[event]]></MsgType>
        <Event><![CDATA[subscribe]]></Event>
        </xml>
         */
        $postStr = null;
        if(isset($GLOBALS['HTTP_RAW_POST_DATA'])){
            $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        }
        if(empty($postStr)){
            $postStr = file_get_contents("php://input");
        }
        $postObj = simplexml_load_string($postStr);
        if(strtolower($postObj->MsgType) == 'event'){
            if(strtolower($postObj->Event) == 'subscribe'){
                //记录关注用户信息（FromUserName），回复用户
                $toUser = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $createTime = time();
                $msgType = 'text';
                $content ='终于等到您！！欢迎关注我们的微信订阅号。';
                //日志
                exception_log('1:msgtype:'.strtolower($postObj->Event).'--event:'.strtolower($postObj->Event).'--openid:'.$toUser.'--serverid:'.$fromUser,'wechat_subscribe');
                $template = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info = sprintf($template,$toUser,$fromUser,$createTime,$msgType,$content);
                //记录新增的订阅用户
                $open_user_info = WechatUserSubscribe::where('open_id',$toUser)->get();
                $wechat = new WechatUserSubscribe;
                if($open_user_info){
                    $wechat->open_id = $toUser;
                    $wechat->server_id = $fromUser;
                    $wechat->is_del = 0;
                    $wechat->created_at = date('Y-m-d H:i:s');
                    $wechat->updated_at = date('Y-m-d H:i:s');
                    $wechat->save();
                }else{
                    $wechat->where('open_id',$toUser)->update(['updated_at' => date('Y-m-d H:i:s')]);
                }
                exception_log('2:msgtype:'.strtolower($postObj->Event).'--event:'.strtolower($postObj->Event).'--openid:'.$toUser.'--serverid:'.$fromUser,'wechat_subscribe');
                echo $info ;
                exit;
            }
        }
        /**
        //消息类型是event，事件
        if(strtolower($postObj->MsgType) == 'event'){
            //如果是订阅事件
            if(strtolower($postObj->Event) == 'subscribe'){
                //记录订阅用户
                $data['open_id'] =  $postObj->FromUserName;
                $data['app_id'] =  $postObj->ToUserName;
                $data['is_del'] =  1;
                $data['created_at'] =  date('Y-m-d H:i:s');
                DB::table('wechat_user_subscribe')->insert($data);
                //记录关注用户信息（FromUserName），回复用户
                $toUser = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $createTime = time();
                $msgType = 'text';
                $content ='终于等到您！！欢迎关注我们的微信订阅号。';

                $template = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info = sprintf($template,$toUser,$fromUser,$createTime,$msgType,$content);
                echo $info ;
            }
        }
**/
    }
    //验证是否来自微信服务器
    private function checkSignature()
    {
        $signature =  !empty($_GET['signature']) ? $_GET['signature']:'';
        $timestamp =  !empty($_GET['timestamp']) ? $_GET['timestamp']:'';
        $nonce =  !empty($_GET['nonce']) ? $_GET['nonce']:'';
        exception_log('signature:'.$signature.'--timestamp:'.$timestamp.'--nonce:'.$nonce,'wechat_check_signatrue');
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