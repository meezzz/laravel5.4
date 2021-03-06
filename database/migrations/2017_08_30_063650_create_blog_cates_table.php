<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogCatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_cates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('分类名称');
            $table->integer('pid')->comment('父级分类');
            $table->string('path')->comment('当前分类从顶级分类下来的路径');
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
        Schema::dropIfExists('blog_cates');
    }
}
