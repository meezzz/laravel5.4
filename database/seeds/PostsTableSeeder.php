<?php

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
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
            $row['title'] = str_random(10);
            $row['content'] = str_random(100);
            $row['user_id'] = rand(1,100);
            $row['created_at'] = date('Y-m-d H:i:d',time());
            $row['updated_at'] = date('Y-m-d H:i:d',time());
            $data[] =$row;
        }
        \DB::table('posts')->insert($data);
    }
}
