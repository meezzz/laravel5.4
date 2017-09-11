<?php

namespace App\Http\Controllers\Workman;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Beanbun\Beanbun;

class TestController extends Controller
{
    //
    //
    public function test(){
        $beanbun = new Beanbun;
        $beanbun->seed = [
            'http://www.950d.com/',
            'http://www.950d.com/list-1.html',
            'http://www.950d.com/list-2.html',
        ];
        $beanbun->afterDownloadPage = function($beanbun) {
            return __DIR__ . '/Info'.date('Ymd').'/' . md5($beanbun->url);
            file_put_contents(__DIR__ . '/Info'.date('Ymd').'/' . md5($beanbun->url), $beanbun->page);
        };
        $beanbun->start();
    }
}
