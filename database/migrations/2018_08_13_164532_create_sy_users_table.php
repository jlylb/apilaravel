<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sy_user', function (Blueprint $table) {
            $table->increments('userid');
            $table->string('username', 30)->comment('用户账号');
            $table->string('userpwd', 32)->comment('用户密码');
            $table->string('avatar', 150)->nullable();
            $table->integer('Co_ID')->unsigned()->nullable();
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
        Schema::drop('sy_user');
    }
}
