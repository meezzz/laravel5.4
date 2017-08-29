<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
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
        $info = ['中国','美国','日本','韩国'];
        foreach ($info as $v){
            $row['name'] = $v;
            $row['created_at'] = date('Y-m-d H:i:d',time());
            $row['updated_at'] = date('Y-m-d H:i:d',time());
            $data[] =$row;
        }
        \DB::table('countries')->insert($data);
    }
}
