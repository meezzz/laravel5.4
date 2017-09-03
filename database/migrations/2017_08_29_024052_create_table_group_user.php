<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGroupUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_user', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('group_id');
            $table->timestamps();
        });
//        if(!Schema::hasTable('group_user')){
//            Schema::create('group_user', function (Blueprint $table) {
//                $table->increments('user_id');
//                $table->integer('group_id');
//                $table->timestamps();
//            });
//        }else{
//            Schema::table('group_user', function ($table) {
//                if(!Schema::hasColumn('user_id'))
//                    $table->increments('user_id');
//                if(!Schema::hasColumn('group_id'))
//                    $table->integer('group_id');
//            });
//        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('group_user');
    }
}
