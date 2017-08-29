<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('users', function (Blueprint $table) {
//                $table->increments('id');
//                $table->string('username');
//                $table->string('email');
//                $table->integer('country_id');
//                $table->string('password');
//                $table->timestamps();
//            });
        if(!Schema::hasTable('users')){
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username');
                $table->string('email');
                $table->integer('country_id');
                $table->string('password');
                $table->integer('mobile');
                $table->timestamps();
            });
        }else{
            Schema::table('users', function ($table) {
                if(!Schema::hasColumn('id'))
                    $table->increments('id');
                if(!Schema::hasColumn('username'))
                    $table->string('username');
                if(!Schema::hasColumn('email'))
                    $table->string('email');
                if(!Schema::hasColumn('country_id'))
                    $table->integer('country_id');
                if(!Schema::hasColumn('password'))
                    $table->string('password');
                if(!Schema::hasColumn('mobile'))
                    $table->integer('mobile');
                if(!Schema::hasColumn('address'))
                    $table->string('address');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
