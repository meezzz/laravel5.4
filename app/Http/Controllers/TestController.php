<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DB;
use   \App\Model\ExceptionLog;
class TestController extends Controller
{
    //测试方法
    public function test()
    {
        //
//        echo 1;
//        $res = DB::table('test')->first();
//        dd($res);
//        $data = array('type'=>'test','content'=>'1111');
        return exception_log('function','test');
    }

}
