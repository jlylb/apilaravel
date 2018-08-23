<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTCompanycnfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_companycnfo', function (Blueprint $table) {
            $table->increments('Co_ID');
            $table->string('Co_Name',150)->comment('公司名');
            $table->string('Co_ConnectionsNumber',60)->comment('公司联系电话');
            $table->string('Co_Logo',100)->nullable()->comment('公司logo');
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
        Schema::drop('t_companycnfo');
    }
}
