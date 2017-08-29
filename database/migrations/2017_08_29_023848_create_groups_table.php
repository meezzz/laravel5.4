<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
//        if(!Schema::hasTable('groups')){
//            Schema::create('groups', function (Blueprint $table) {
//                $table->increments('id');
//                $table->string('name');
//                $table->timestamps();
//            });
//        }else{
//            Schema::table('groups', function ($table) {
//                if(!Schema::hasColumn('id'))
//                    $table->increments('id');
//                if(!Schema::hasColumn('name'))
//                    $table->string('name');
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
        Schema::dropIfExists('groups');
    }
}
