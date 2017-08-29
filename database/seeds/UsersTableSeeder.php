<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = array();
        for ($i=0;$i<100;$i++){
            $row['username'] = str_random(5);
            $row['email'] = rand(1000000,999999999).'@qq.com';
            $row['password'] = md5(rand(10000000,99999999));
            $mobile = ['130','137','159','183','173','159'];
            $row['mobile'] = array_rand($mobile,1).rand(10000000,99999999);
            $row['country_id'] = rand(1,10);
            $row['created_at'] = date('Y-m-d H:i:d',time());
            $row['updated_at'] = date('Y-m-d H:i:d',time());
            $data[] =$row;
        }
        \DB::table('users')->insert($data);
    }
}
