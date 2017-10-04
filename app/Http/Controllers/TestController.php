<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DB;
class TestController extends Controller
{
    //测试方法
    public function test()
    {
        //
        echo 1;
        $res = DB::table('test')->first();
        dd($res);
    }

}
