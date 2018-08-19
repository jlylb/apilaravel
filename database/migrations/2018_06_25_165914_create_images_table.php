<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户编号');
            $table->string('name',100)->comment('图片名');
            $table->string('path',255)->comment('图片路径');
            $table->string('mime_type',30)->comment('图片类型');
            $table->integer('size')->default(0)->comment('图片大小');
            $table->tinyInteger('type')->default(1)->comment('图片分类 1=头像 2=封面');
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
        Schema::dropIfExists('images');
    }
}
