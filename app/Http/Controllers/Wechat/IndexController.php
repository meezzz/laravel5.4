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
    const APPID = 'wx3f33a35abedfd301';
    const APP_SECRET='5096d92b62cc9b1e8c9e06554c58ed0f';

    //用户发给公众号的消息以及开发者需要的事件推送，将被微信转发到该方法中
    public function index(){
        //验证消息的确来自微信服务器
        $echostr = isset($_GET['echostr']) ? $_GET['echostr'] : '';
        $is_from_wechat_server = $this->checkSignature();
        $content=array(
            'is_from_wexin_server'=>$is_from_wechat_server,
            'echostr' => $echostr,
            'get'=>$_GET
        );
        exception_log($content,'wechat_index');
        if($is_from_wechat_server && $echostr){
            return $echostr;
        }else{
            $this->responseMsg();
        }
        return '';
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
        exception_log(['posttr'=>$postStr],'wechat_response_test1');
        $postObj = simplexml_load_string($postStr);
        exception_log(['posobj'=>$postObj],'wechat_response_test2');
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        if(strtolower($postObj->MsgType) == 'event'){
            if(strtolower($postObj->Event) == 'subscribe'){
                //订阅事件
                //记录关注用户信息（FromUserName），回复用户
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
                //拼接订阅事件返回给微信服务器的字符串
                $res_info = sprintf($template,$toUser,$fromUser,$createTime,$msgType,$content);
                //记录新增的订阅用户
                $open_user_info = WechatUserSubscribe::where('open_id',$toUser)->get();
                $wechat = new WechatUserSubscribe;
                //如果从未订阅过，则直接记录新用户。
                if(!$open_user_info){
                    $wechat->open_id = $toUser;
                    $wechat->server_id = $fromUser;
                    $wechat->unsubscribe = 1;
                    $wechat->created_at = date('Y-m-d H:i:s');
                    $wechat->updated_at = date('Y-m-d H:i:s');
                    $wechat->save();
                    exception_log('2:msgtype:'.strtolower($postObj->Event).'--event:'.strtolower($postObj->Event).'--openid:'.$toUser.'--serverid:'.$fromUser,'wechat_subscribe');
                }else{
                    exception_log('3:msgtype:'.strtolower($postObj->Event).'--event:'.strtolower($postObj->Event).'--openid:'.$toUser.'--serverid:'.$fromUser,'wechat_subscribe');
                    $wechat->where('open_id',$toUser)->update(['subscribe' => 1]);
                }
                exception_log('4:msgtype:'.strtolower($postObj->Event).'--event:'.strtolower($postObj->Event).'--openid:'.$toUser.'--serverid:'.$fromUser,'wechat_subscribe');
                echo $res_info ;
                exit;
            }elseif (strtolower($postObj->Event) =='unsubscribe'){
                //取消订阅事件
                exception_log('1:msgtype:'.strtolower($postObj->Event).'--event:'.strtolower($postObj->Event).'--openid:'.$toUser.'--serverid:'.$fromUser,'wechat_unsubscribe');
                $wechat = new WechatUserSubscribe;
                $wechat->where('open_id',$toUser)->update(['subscribe' => 0]);
            }elseif(strtolower($postObj->Event) =='location'){
                //上报地理位置
            }
        }elseif (strtolower($postObj->MsgType) == 'text'){
            if(strtolower($postObj->Content) =='图文'){

            }else{
                //接收普通纯文本消息
                $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        </xml>";
                switch (strtolower($postObj->Content)){
                    case 'hello':
                        $content ='hello sir';
                        break;
                    case '天气':
                        $content ='天气很好！';
                        break;
                    case '百度':
                        $content ='<a href="https://www.baidu.com/">百度首页</a>';
                        break;
                    default:
                        $content ='虽然我很聪明，但是您的问题还是问倒我了。。';
                }
                $createTime = time();
                $msgType = 'text';
                exception_log('1:msgtype:'.strtolower($postObj->Event).'--event:'.strtolower($postObj->Event).'--content'.$postObj->Content.'--openid:'.$toUser.'--serverid:'.$fromUser,'wechat_text');
                $res_info = sprintf($template,$toUser,$fromUser,$createTime,$msgType,$content);
                echo $res_info;exit;
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