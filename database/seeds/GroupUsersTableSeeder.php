<?php

use Illuminate\Database\Seeder;

class GroupUsersTableSeeder extends Seeder
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
            $row['user_id'] = $i+1;
            $row['group_id'] = rand(1,10);
            $row['created_at'] = date('Y-m-d H:i:d',time());
            $row['updated_at'] = date('Y-m-d H:i:d',time());
            $data[] =$row;
        }
        \DB::table('group_user')->insert($data);
    }
}
