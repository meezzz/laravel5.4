<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserinfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userinfos', function (Blueprint $table) {
                $table->increments('user_id');
                $table->string('qq');
                $table->string('xingzuo');
                $table->timestamps();
            });
//        if(!Schema::hasTable('userinfos')){
//            Schema::create('userinfos', function (Blueprint $table) {
//                $table->increments('user_id');
//                $table->string('qq');
//                $table->string('xingzuo');
//                $table->timestamps();
//            });
//        }else{
//            Schema::table('userinfos', function ($table) {
//                if(!Schema::hasColumn('user_id'))
//                    $table->increments('user_id');
//                if(!Schema::hasColumn('qq'))
//                    $table->string('qq');
//                if(!Schema::hasColumn('xingzuo'))
//                    $table->string('xingzuo');
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
        Schema::dropIfExists('userinfos');
    }
}
