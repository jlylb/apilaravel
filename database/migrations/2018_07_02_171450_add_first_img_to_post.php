<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFirstImgToPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('first_img',100)->nullable()->after('uuid');
            $table->Integer('view_num')->default(0)->after('first_img')->comment('浏览数量');
            $table->Integer('comment_num')->default(0)->after('view_num')->comment('评论数量');
            $table->boolean('comment_status')->default(true)->after('comment_num')->comment('是否允许评论');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('first_img');
            $table->dropColumn('view_num');
            $table->dropColumn('comment_num');
            $table->dropColumn('comment_status');
        });
    }
}
