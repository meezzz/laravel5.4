<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DB;
use App\Model\User;

class UserController extends Controller
{

    //模型关联
    public function relation(){
        $user =  User::find(1);
        $res = $user->userinfo()->first();
        $res = $user->userinfo;
        //一对多关联
        $res = $user->post()->where('id','=',22)->get();
        $res = $user->post; //没有附加操作可以直接这样用属性
        //属于关系
        $res = $user->country()->get();
        $res = $user->country;
        //多对多关系
        $res = $user->group()->get();
        dd($res);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        echo "create";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
