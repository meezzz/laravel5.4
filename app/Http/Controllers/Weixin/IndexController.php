<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Model\Weixin\WeixinUserSubscribe;
use Illuminate\Support\Facades\Redis;
class IndexController extends Controller
{
    //微信测试账号

    const TOKEN = 'weixin';
    const APPID = 'wxcb42df271f31af3d';
    const APP_SECRET='bd1cb35afb8d1336dc2e1d8dccd38400';
    const APP_NAME = 'weixintest';
    public $click_key_conf = array(
        0=>[
            'huochepiao'=>self::APP_NAME.'_huochepiao',
            'feijipiao'=>self::APP_NAME.'_feijipiao'
        ],
        1=>[

        ],
        2=>[

        ]
    );

    //用户发给公众号的消息以及开发者需要的事件推送，将被微信转发到该方法中
    public function index(){
        $is_from_wexin_server = $this->checkSignature();
        //验证消息的确来自微信服务器
        if($is_from_wexin_server ){
            $echostr = !empty($_GET['echostr']) ? $_GET['echostr'] : '';

            $content=array(
                'is_from_wexin_server'=>$is_from_wexin_server,
                'echostr' => $echostr,
                'get'=>$_GET
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
                //记录新增的订阅用户
                $open_user_info = WeixinUserSubscribe::where('open_id',$toUser)->first();
                $weixin = new WeixinUserSubscribe;
                //如果从未订阅过，则直接记录新用户。
                if(!$open_user_info){
                    $weixin->open_id = $toUser;
                    $weixin->server_id = $fromUser;
                    $weixin->subscribe = 1;
                    $weixin->created_at = date('Y-m-d H:i:s');
                    $weixin->updated_at = date('Y-m-d H:i:s');
                    $weixin->save();
                }else{
                    $weixin->where('open_id',$toUser)->update(['subscribe' => 1]);
                }
                echo $res_info ;
                exit;
            }elseif (strtolower($postObj->Event) =='unsubscribe'){
                //取消订阅事件
                $weixin = new WeixinUserSubscribe;
                $weixin->where('open_id',$toUser)->update(['subscribe' => 0]);
            }elseif (strtolower($postObj->Event) =='click'){
                //事件是自定义菜单栏的click事件
                $click_key_conf = $this->click_key_conf;
                if(strtolower($postObj->EventKey) ==$click_key_conf[0]['huochepiao']){

                }
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
    }

    //自定义微信菜单栏
    public function defindItem(){
        //目前微信接口的调用方式都是通过curl get/post
        $access_token = $this->getWeixinAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        $post_arr = array(
            'button'=>array(
                //第1个一级菜单
                array(
                    'type'=>'click',
                    'name'=>urlencode('火车机票'),
                    'sub_button'=>array(
                        array(
                            'type'=>'view',
                            'name'=>urlencode('百度搜索'),
                            'url' =>'http://www.baidu.com/'
                        ),
                        array(
                            'type'=>'click',
                            'name'=>urlencode('火车票'),
                            'key'=>$this->click_key_conf[0]['huochepiao']
                        ),
                        array(
                            'type'=>'click',
                            'name'=>urlencode('飞机票'),
                            'key'=>$this->click_key_conf[0]['feijipiao']
                        )
                    )
                ),
                //第2个一级菜单
                array(
                    'type'=>'view',
                    'name'=>urlencode('网易云音乐'),
                    'url'=>'http://music.163.com/m/'
                ),

                //第3个一级菜单
                array(
                    'type'=>'view',
                    'name'=>urlencode('糗事百科'),
                    'url'=>'https://www.qiushibaike.com/'
                )
            ),
        );
        $post_json = urldecode(json_encode($post_arr));
        $res = http_curl($url,'post','json',$post_json);
       return $res;
    }

    //群发接口
    public function sendMsgAll(){
        //1：获得全局accesstoken
        $access_token = $this->getWeixinAccessToken();
        //2：组装群发数据数组
        $msg_arr = array(
            'touser'=>'o0JjWw6iwk3WJuzm3o5zRkfpPYQ4',
            'msgtype'=>'text',
            'text'=>array(
                'content'=>'This is Group sent message!!!'
            )
        );
        //3：将数组转换成json
        $msg_json = \GuzzleHttp\json_encode($msg_arr);
        //4：调用curl发送
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$access_token;
        $res = http_curl($url,'post','json',$msg_json);
        return $res;
    }

    //获取用户的openid
    public function getUserBaseInfo(){
        //1：获取code
        $redirect_uri = urlencode('http://laravel.supwlz.ml/weixin/getUserOpenId');
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.self::APPID.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
        header('location:'.$url);
    }
    public function getUserOpenId(){
        //通过code换取网页授权access_token
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.self::APPID.'&secret='.self::APP_SECRET.'&code='.$_GET['code'].'&grant_type=authorization_code';
        $res = http_curl($url,'get');
        exception_log($res,'weixin_getUserOpenId');
        $login_model = new \App\Model\Weixin\WeixinWebPageAuthorizationLogin;
        $open_user_info =$login_model->where('openid',$res['openid'])->first();
        //如果从未订阅过，则直接记录新用户。
        if(!$open_user_info){
            $login_model->openid = $res['openid'];
            $login_model->scope = $res['scope'];
            $login_model->access_token =$res['access_token'];
            $login_model->refresh_token =$res['refresh_token'];
            $login_model->created_at = date('Y-m-d H:i:s');
            $login_model->updated_at = date('Y-m-d H:i:s');
            $login_model->save();
        }else{
            $login_model->where('open_id',$toUser)->update(['subscribe' => 1]);
        }
    }
    //JS-SDK
    //微信分享
    public function shareWx(){
        $jsapi_ticket = $this->getWeixinJsApiTicket();
        $timestamp = time();
        $nonceStr = getRandomString(16);
        $url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $signature_str = "jsapi_ticket=".$jsapi_ticket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url;
        $signature = sha1($signature_str);
        $info['name']= '微信JssS-SDK测试';
        $info['appid']= self::APPID;
        $info['timestamp']= $timestamp;
        $info['nonceStr']= $nonceStr;
        $info['signature']= $signature;

        $info['jsapi_ticket']= $jsapi_ticket;
        $info['url']= 'http://laravel.supwlz.ml/weixin/shareWx';
        $info['signature_str']= $signature_str;
        exception_log($info,'weixin_sharewx');
        return view('weixin.sharewx', ['info' => $info]);
    }

    //微信jsapi_ticket
    public function getWeixinJsApiTicket(){
        $jsapi_ticket_key = $this->getWeixinJsApiTicketRedisKey();
        $jsapi_ticket = Redis::get($jsapi_ticket_key);
        if(!empty($jsapi_ticket))
            return $jsapi_ticket;
        $access_token = $this->getWeixinAccessToken();
        //获取jsapi_ticket
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=wx_card";
        $jsapi_ticket = http_curl($url,'get','json');
        Redis::set($jsapi_ticket_key,$jsapi_ticket['ticket']);
        //过期时间为返回的存活时间-60秒
        Redis::expire($jsapi_ticket_key, $jsapi_ticket['expires_in']-60);
        //存储到redis中
        return $jsapi_ticket['ticket'];
    }
    //获取微信服务器的ip地址
    public function getWeixinServerIp(){
        $access_token =  $this->getWeixinAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$access_token;
        $server_ips = http_curl($url);
        $server_ips_arr = json_decode($server_ips,true);
        return $server_ips_arr['ip_list'];
    }
    //获取测试号微信access_token
    public function getWeixinAccessToken(){
        $access_token_key = $this->getWeixinAccessTokenRedisKey();
        $access_token = Redis::get($access_token_key);
        if(!empty($access_token))
            return $access_token;
        $appid =self::APPID;
        $appsecret = self::APP_SECRET;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $access_token = http_curl($url,'get','json');
        Redis::set($access_token_key,$access_token['access_token']);
        //过期时间为返回的存活时间-60秒
        Redis::expire($access_token_key, $access_token['expires_in']-60);
        //存储到redis中
        return $access_token['access_token'];
    }

    /*************************    private  *********************************/

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

    private function getWeixinJsApiTicketRedisKey(){
        return self::APPID.'_'.self::APP_SECRET.'_jsapi_ticket';
    }
    private function getWeixinAccessTokenRedisKey(){
        return self::APPID.'_'.self::APP_SECRET.'_accessstoken';
    }
}
