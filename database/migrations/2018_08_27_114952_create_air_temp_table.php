<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_realdata_air', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->float('rd_temp',9,2)->default('0.0')->comment('温度');
            $table->float('rd_wet',9,2)->default('0.0')->comment('湿度');
        });
        
        Schema::create('t_hisdata_air', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->float('hd_temp',9,2)->default('0.0')->comment('温度');
            $table->float('hd_wet',9,2)->default('0.0')->comment('湿度');
        });
        
        Schema::create('t_realdata_liquid', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->float('rd_level',9,2)->default('0.0')->comment('水位');
        });
        
        Schema::create('t_hisdata_liquid', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->float('hd_level',9,2)->default('0.0')->comment('水位');
        });
        
        Schema::create('t_realdata_soil', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->float('rd_temp',9,2)->default('0.0')->comment('土壤温度');
            $table->float('rd_salt',9,2)->default('0.0')->comment('土壤盐碱');
        });
        
        Schema::create('t_hisdata_soil', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->float('hd_temp',9,2)->default('0.0')->comment('土壤温度');
            $table->float('hd_salt',9,2)->default('0.0')->comment('土壤盐碱');
        });
        
        Schema::create('t_realdata_light', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->float('rd_light_intensity',9,2)->default('0.0')->comment('光照强度');
        });
        
        Schema::create('t_hisdata_light', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->float('hd_light_intensity',9,2)->default('0.0')->comment('光照强度');
        });
        
        Schema::create('t_realdata_co2', function (Blueprint $table) {
            $table->dateTime('rd_updatetime')->nullable();
            $table->integer('pdi_index')->unsinged()->unique();
            $table->float('rd_co2_concentration',9,2)->default('0.0')->comment('二氧化碳浓度');
        });
        
        Schema::create('t_hisdata_co2', function (Blueprint $table) {
            $table->increments('hd_index');
            $table->dateTime('rd_updatetime')->nullable();
            $table->dateTime('hd_datetime')->nullable();
            $table->integer('pdi_index')->unsinged()->default(0);
            $table->float('hd_co2_concentration',9,2)->default('0.0')->comment('二氧化碳浓度');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('t_realdata_air');
        Schema::drop('t_hisdata_air');
        
        Schema::drop('t_realdata_liquid');
        Schema::drop('t_hisdata_liquid');
        
        Schema::drop('t_realdata_soil');
        Schema::drop('t_hisdata_soil');
        
        Schema::drop('t_realdata_light');
        Schema::drop('t_hisdata_light');
        
        Schema::drop('t_realdata_co2');
        Schema::drop('t_hisdata_co2');
    }
}
