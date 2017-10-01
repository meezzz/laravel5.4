<?php

use Illuminate\Database\Seeder;

class BlogUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array();
        for ($i=0;$i<10;$i++){
            $row['username'] = str_random(5);
            $row['password'] = md5(rand(10000000,99999999));
            $row['email'] = rand(100000,99999999999).'@qq.com';
            $row['profile'] ='/Uploads/20171001/11_59d0663f00c4377464.jpg';
            $row['intro'] =str_random(30);
            $row['created_at'] = date('Y-m-d H:i:d',time());
            $row['updated_at'] = date('Y-m-d H:i:d',time());
            $data[] =$row;
        }
        \DB::table('blog_users')->insert($data);
    }
}
