<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('password',60);
            $table->string('email',60)->unique();
            $table->rememberToken();
            $table->string('profile')->comment('头像路径');
            $table->text('intro')->comment('个人介绍');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_users');
    }
}
