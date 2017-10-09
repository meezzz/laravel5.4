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

    function http_get($url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $output = curl_exec($ch);
        curl_close($ch);
        if(curl_errno($ch)){
            exception_log(curl_error($ch),'curl_get_errror');
            return false;
        }
        return $output;
    }
    //获取微信access_token
    function getWechatAccessToken(){
        $appid = App\Http\Controllers\Wechat\IndexController::APPID;
        $appsecret = App\Http\Controllers\Wechat\IndexController::APP_SECRET;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $access_token = http_get($url);
        return $access_token;
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
