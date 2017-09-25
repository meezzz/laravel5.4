<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    //后台首页
    public function index(){
        return view('admin.index');
    }
    public function test(){
        return ' App\Http\Controllers\Admin admin test';
    }

}
