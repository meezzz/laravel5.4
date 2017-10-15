<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Model\Weixin\WeixinUserSubscribe;
class IndexController extends Controller
{
    //

    const TOKEN = 'weixin';
    const APPID = 'wxcb42df271f31af3d';
    const APP_SECRET='bd1cb35afb8d1336dc2e1d8dccd38400';

    //用户发给公众号的消息以及开发者需要的事件推送，将被微信转发到该方法中
    public function index(){
        $is_from_wexin_server = $this->checkSignature();
        //验证消息的确来自微信服务器
        if($is_from_wexin_server ){
            $echostr = !empty($_GET['echostr']) ? $_GET['echostr'] : '';

            $content=array(
                'is_from_wexin_server'=>$is_from_wexin_server,
                'echostr' => $echostr
            );
            exception_log($content,'weixin_index');
            if($echostr){
                return $echostr;
            }else{
                $this->responseMsg();
            }
        }
        return 'No access';
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
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $exlog_content=array(
            'toUser'=>$toUser,
            'fromUser'=>$fromUser,
            'event' =>$postObj->Event,
            'MsgType'=>$postObj->Event
        );
        exception_log($exlog_content,'weixin_response');
        if(strtolower($postObj->MsgType) == 'event'){
            if(strtolower($postObj->Event) == 'subscribe'){
                //订阅事件
                //记录关注用户信息（FromUserName），回复用户
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
                //拼接订阅事件返回给微信服务器的字符串
                $res_info = sprintf($template,$toUser,$fromUser,$createTime,$msgType,$content);
                $exlog_content=array(
                    'res_info'=>$res_info,
                    'event' =>$postObj->Event
                );
                exception_log($exlog_content,'weixin_subscribe_1');
                //记录新增的订阅用户
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
                echo $res_info ;
                exit;
            }elseif (strtolower($postObj->Event) =='unsubscribe'){
                //取消订阅事件
                $wexin = new WeixinUserSubscribe;
                $wexin->where('open_id',$toUser)->update(['subscribe' => 0]);
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


        $token = self::TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        $content=array(
            'signature'=>$signature,
            'timestamp'=> $timestamp,
            'nonce'=> $nonce,
            'tmpstr'=>$tmpStr
        );
        exception_log($content,'weixin_check_signature');
        if( $tmpStr == $signature )
            return true;
        return false;
    }
}
