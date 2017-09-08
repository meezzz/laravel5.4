<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(GroupUsersTableSeeder::class);
        $this->call(PostsTableSeeder::class);
        $this->call(UserinfosTableSeeder::class);
          $this->call(BlogUsersTableSeeder::class);
    }
}
