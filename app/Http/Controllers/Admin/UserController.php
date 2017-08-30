<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use  App\Model\BlogUser;
class UserController extends Controller
{
    //用户添加页面显示
    public function add(){
        return view('admin.user.add');
    }
    //用户列表
    public function index(Request $request){
       $data=array();
       $users = BlogUser::paginate(10);
       $data['request'] = $request;
       $data['users'] = $users;
        return view('admin.user.index',$data);
    }
    //用户插入操作
    public function insert(Request $request){
        $data = $request->all();
    }
}
