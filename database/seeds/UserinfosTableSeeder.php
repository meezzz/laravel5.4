<?php

use Illuminate\Database\Seeder;

class UserinfosTableSeeder extends Seeder
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
            $row['qq'] = rand(1000000,999999999);
            $xingnzuo = ['巨蟹','天平','金牛','处女','天蝎','射手'];
            $row['xingzuo'] =array_rand($xingnzuo,1);
            $row['created_at'] = date('Y-m-d H:i:d',time());
            $row['updated_at'] = date('Y-m-d H:i:d',time());
            $data[] =$row;
        }
        \DB::table('userinfos')->insert($data);
    }
}
