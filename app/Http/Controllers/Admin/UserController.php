<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use  App\Model\BlogUser;
use Hash;
class UserController extends Controller
{
    //用户列表
    public function index(Request $request){
        //每页显示的数量
        $data=array();
        //搜索条件
        //where方式2：匿名函数
        $users = BlogUser::orderBy('id', 'asc')  //排序
        ->where(function ($query) use ($request){  //where条件，可以直接使用，也可以
            $keyword = $request->input('keyword');
            if(!empty($keyword)){
                $query->where('username','like','%'.$keyword.'%');
            }
        })
            ->paginate($request->input('num',10));
//        $users = BlogUser::get();
        $data['request'] = $request;
        $data['users'] = $users;
        return view('admin.user.index',$data);
    }
    //用户添加页面显示
    public function add(){
        return view('admin.user.add');
    }
    //用户插入操作
    public function doadd(Request $request){
        //表单验证
        $this->validate($request, [
            'username' => 'required|regex:/\w{3,20}/|max:255', //用户名不能为空，正则验证
            'email'=>'required|email',
            'repassword'=>'same:password',
        ],[
            'username.required'=>'用户名不能为空',
            'username.regex'=>'用户名填写不正确，必须为3-20位字母数组下划线',
            'email.required'=>'邮箱不能为空',
            'email.email'=>'邮箱格式不正确',
            'repassword.same'=>'两次密码不一致',
        ]);
        //插入数据库
        $user = new BlogUser;
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password'));
        $user->email = $request->input('email');
        $user->intro = $request->input('intro');
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        //上传图片
        if ($request->hasFile('profile')) {
            //
            $path = './Uploads/'.date('Ymd');
            $file_name  =  date('H').'_'.uniqid().rand(10000,99999);
            //文件后缀
            $surfix = $request->file('profile')->getClientOriginalExtension();
            $file_name = $file_name . '.'.$surfix;
            $request->file('profile')->move($path,$file_name);
            //图像位置
            $user->profile = trim($path.'/'.$file_name,'.');
        }
            if($user->save()){
                return redirect('admin/user/index')->with('info','添加成功');
            }else{
                return back()->with('info','添加失败');
            }
    }
    //用户修改
    public function edit($id){
        $user = BlogUser::findOrFail($id);
        $data['user'] = $user;
        return view('admin.user.edit',$data);
    }
    public function doedit(Request $request){
        //表单验证
        $this->validate($request, [
            'username' => 'required|regex:/\w{3,20}/|max:255', //用户名不能为空，正则验证
            'email'=>'required|email',
        ],[
            'username.required'=>'用户名不能为空',
            'username.regex'=>'用户名填写不正确，必须为3-20位字母数组下划线',
            'email.required'=>'邮箱不能为空',
            'email.email'=>'邮箱格式不正确',
        ]);
        $user =  BlogUser::find($request->input('id'));
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->intro = $request->input('intro');
        $user->updated_at = date('Y-m-d H:i:s');
        //上传图片
        if ($request->hasFile('profile')) {
            //
            $path = './Uploads/'.date('Ymd');
            $file_name  =  date('H').'_'.uniqid().rand(10000,99999);
            //文件后缀
            $surfix = $request->file('profile')->getClientOriginalExtension();
            $file_name = $file_name . '.'.$surfix;
            $request->file('profile')->move($path,$file_name);
            //图像位置
            $user->profile = trim($path.'/'.$file_name,'.');
        }
        if($user->save()){
            return redirect('admin/user/index')->with('info','修改成功');
        }else{
            return back()->with('info','修改失败');
        }
    }
    public function delete($id){
        $user = BlogUser::findOrFail($id);
        //头像
        $path = '.'.$user->profile;
        if(file_exists($path))
            unlink($path);
        if($user->delete()){
            return back()->with('info','删除成功');
        }else{
            return back()->with('info','删除失败');
        }
    }


}
