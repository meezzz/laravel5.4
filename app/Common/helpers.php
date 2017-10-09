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
        $exception_log->content = $content;
        $exception_log->type = $type;
        $exception_log->created_at = date('Y-m-d H:i:s',time());
        $exception_log->updated_at = date('Y-m-d H:i:s',time());
        return  $exception_log->save();
    }