<?php
/**
 * Created by PhpStorm.
 * User: Myron
 * Date: 2017/9/28
 * Time: 15:39
 */
use \App\Model\ExceptionLog;

    function a_dump($info){
         var_dump($info);
    }

/**
 * @param $content 日志内容
 * @param $type    日志索引标识
 * @return bool
 */
    function exception_log($content,$type){
        $exception_log = new ExceptionLog;
        $exception_log->content = serialize($content);
        $exception_log->type = $type;
        $exception_log->created_at = date('Y-m-d H:i:s',time());
        $exception_log->updated_at = date('Y-m-d H:i:s',time());
        return  $exception_log->save();
    }

/**
 * @param $url 接口url string
 * @param string $type 请求类型
 * @param string $res_type 请求返回数据类型格式
 * @param string $arr  post请求参数
 * @return string
 */
    function http_curl($url,$type='get',$res_type='json',$arr=''){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if($type=='post'){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        $output = curl_exec($ch);
        if(curl_errno($ch)){
            exception_log(curl_error($ch),'curl_get_errror');
        }
        $return = '';
        if($res_type=='json'){
            if(curl_errno($ch)){
                $return =  curl_error($ch);
            }else{
                $return =  json_decode($output,true);
            }
        }elseif ($res_type=='xml'){

        }
        curl_close($ch);
        return $return ;
    }
    //获取微信access_token
    function getWechatAccessToken(){
        $appid = App\Http\Controllers\Wechat\IndexController::APPID;
        $appsecret = App\Http\Controllers\Wechat\IndexController::APP_SECRET;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $access_token = http_get($url);
        $access_token = json_decode($access_token,true);
        return $access_token['access_token'];
    }

    //获取微信服务器的ip地址
    function getWechatServerIp(){
       $access_token =  getWechatAccessToken();
       $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$access_token;
        $server_ips = http_get($url);
        $server_ips_arr = json_decode($server_ips,true);
        return $server_ips_arr['ip_list'];
    }
    /**
     * post请求url，并返回结果
     * @param $url
     * @param $data
     * @param int $timeout
     * @param bool $useCookie
     * @param array $header_ex
     * @return bool|mixed
     */
    function post($url,$data,$timeout=10,$useCookie=false, $header_ex= array())
    {
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL,$url);
        $header = array(
            "Content-Type: application/x-www-form-urlencoded; charset=utf-8",
            "Accept:application/json, text/javascript, */*; q=0.01"
        );
        $header = array_merge($header, $header_ex);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch,CURLOPT_HEADER,false);

        if($useCookie){//使用当前请求的cookie
            curl_setopt($ch,CURLOPT_COOKIE,$_SERVER['HTTP_COOKIE']);
        }

        curl_setopt($ch,CURLOPT_AUTOREFERER,true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
        curl_setopt($ch,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);

        //execute post
        $response = curl_exec($ch);
        //get response code
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //close connection
        curl_close($ch);

        //return result
        if($response_code == '200') {
            return $response;
        } else {
            try{
                $exception['response_code'] = $response_code;
                $exception['response'] = $response;
                $exception['url'] = $url;
                $exception['data'] = $data;
                $exception['header'] = $header;
                exception_log($exception, 'curl_error');
            } catch(Exception $e){
                exception_log('日志记录修改出错，$response_code=' . $response_code, 'curl_error');
                //---
            }
            return false;
        }
    }

    /**生成固定长度的随机字符串
     * @param $len 长度
     * @return string
     */
    function getRandomString($len){
        $str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $randomStr = "";
        for($i=0;$i<$len;$i++)
        {
            $randomStr .= $str{mt_rand(0,strlen($str)-1)};    //生成php随机数
        }
        return $randomStr;
    }


