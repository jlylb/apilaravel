<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlDevice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_realdata_juanlian', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });
        
        Schema::create('t_hisdata_juanlian', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });
        
        Schema::create('t_realdata_guangai', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });
        
        Schema::create('t_hisdata_guangai', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });
        
        
        Schema::create('t_realdata_shifei', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });
        
        Schema::create('t_hisdata_shifei', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });

        
        Schema::create('t_realdata_tiaowen', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });
        
        Schema::create('t_hisdata_tiaowen', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        }); 

        
        Schema::create('t_realdata_tongfei', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });
        
        Schema::create('t_hisdata_tongfei', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });
        
        Schema::create('t_realdata_buguang', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });
        
        Schema::create('t_hisdata_buguang', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->tinyInteger('device_status')->default(1)->comment('设备状态 1=停止 0=正常');
            $table->tinyInteger('running_status')->default(1)->comment('运行状态 1=关闭 0=打开');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('t_realdata_juanlian');
        Schema::drop('t_hisdata_juanlian');
        
        Schema::drop('t_realdata_guangai');
        Schema::drop('t_hisdata_guangai');
        
        Schema::drop('t_realdata_shifei');
        Schema::drop('t_hisdata_shifei');
        
        Schema::drop('t_realdata_tiaowen');
        Schema::drop('t_hisdata_tiaowen');
        
        Schema::drop('t_realdata_tongfei');
        Schema::drop('t_hisdata_tongfei');
        
        Schema::drop('t_realdata_buguang');
        Schema::drop('t_hisdata_buguang');
    }
}
