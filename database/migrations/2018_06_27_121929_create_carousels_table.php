<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarouselsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carousels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('幻灯片标题');
            $table->string('height')->default('auto')->comment('显示高度');
            $table->tinyInteger('initial_index')->default(0)->comment('幻灯片的索引');
            $table->boolean('autoplay')->default(false)->comment('是否自动切换');
            $table->smallInteger('interval')->default(3000)->comment('自动切换的时间间隔，单位为毫秒');
            $table->enum('type',['type','none'])->default('none')->comment('走马灯的类型');
            $table->enum('indicator_position',['outside','none'])->default('none')->comment('指示器的位置');
            $table->enum('trigger',['click','hover'])->default('click')->comment('指示器的触发方式');
            $table->enum('arrow',['hover','always', 'never'])->default('hover')->comment('切换箭头的显示时机');
            $table->boolean('status')->default(true)->comment('幻灯片是否启用');
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
        Schema::dropIfExists('carousels');
    }
}
