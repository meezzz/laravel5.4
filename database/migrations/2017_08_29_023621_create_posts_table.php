<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('content');
            $table->integer('user_id');
            $table->timestamps();
        });
//        if(!Schema::hasTable('posts')){
//                Schema::create('posts', function (Blueprint $table) {
//                    $table->increments('id');
//                    $table->string('title');
//                    $table->text('content');
//                    $table->integer('user_id');
//                    $table->timestamps();
//                });
//        }else{
//                Schema::table('posts', function ($table) {
//                    if(!Schema::hasColumn('id'))
//                        $table->increments('id');
//                    if(!Schema::hasColumn('title'))
//                        $table->string('title');
//                    if(!Schema::hasColumn('content'))
//                        $table->text('content');
//                    if(!Schema::hasColumn('user_id'))
//                        $table->integer('user_id');
//                });
//            }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
